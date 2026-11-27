<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Expertise;
use App\Models\Hobby;
use App\Models\Portfolio;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Setup;
use App\Models\Skill;
use App\Models\Social;
use App\Models\Testimonial;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'string', 'in:user,assistant,system'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        /** @var list<array{role: string, content: string}> $messages */
        $messages = collect($validated['messages'])
            ->filter(fn (array $m): bool => $m['role'] !== 'system')
            ->take(-10)
            ->map(fn (array $m): array => [
                'role' => $m['role'],
                'content' => $m['content'],
            ])
            ->values()
            ->all();

        $geminiKey = config('services.gemini.key');
        $groqKey = config('services.groq.key');

        if (empty($geminiKey) && empty($groqKey)) {
            return response()->json([
                'error' => 'AI service is not configured.',
            ], 500);
        }

        $systemInstruction = 'You are the official AI assistant for this portfolio website. '
            ."Answer concisely, politely, in the same language as the user's message, and ONLY using the CV data below. "
            ."If a question cannot be answered from the data, say you don't have that information.\n\n"
            .$this->buildCvContext();

        $lastStatus = 500;

        if (! empty($geminiKey)) {
            $geminiResult = $this->callGemini((string) $geminiKey, $systemInstruction, $messages);

            if ($geminiResult['ok']) {
                return $this->successResponse($geminiResult);
            }

            $lastStatus = $geminiResult['status'];
        }

        if (! empty($groqKey)) {
            $groqResult = $this->callGroq((string) $groqKey, $systemInstruction, $messages);

            if ($groqResult['ok']) {
                return $this->successResponse($groqResult);
            }

            $lastStatus = $groqResult['status'];
        }

        return $this->errorResponse($lastStatus);
    }

    /**
     * Send the conversation to Gemini as the primary provider.
     *
     * @param  list<array{role: string, content: string}>  $messages
     * @return array{ok: bool, reply: ?string, provider: string, model: string, status: int}
     */
    private function callGemini(string $apiKey, string $systemInstruction, array $messages): array
    {
        // Map "assistant" -> "model" for Gemini's generateContent API
        $contents = array_map(fn (array $m): array => [
            'role' => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ], $messages);

        $model = (string) config('services.gemini.model', 'gemini-3.8-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        return $this->executeProviderRequest(
            'Gemini',
            $model,
            fn (): Response => Http::connectTimeout(5)->timeout(20)->post($url, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
            ]),
            fn (Response $response): string => (string) ($response->json('candidates.0.content.parts.0.text') ?? ''),
        );
    }

    /**
     * Send the conversation to Groq as the fallback provider.
     *
     * @param  list<array{role: string, content: string}>  $messages
     * @return array{ok: bool, reply: ?string, provider: string, model: string, status: int}
     */
    private function callGroq(string $apiKey, string $systemInstruction, array $messages): array
    {
        $groqMessages = [
            ['role' => 'system', 'content' => $systemInstruction],
            ...$messages,
        ];

        $model = (string) config('services.groq.model', 'openai/gpt-oss-20b');

        return $this->executeProviderRequest(
            'Groq',
            $model,
            fn (): Response => Http::withToken($apiKey)
                ->connectTimeout(5)
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $groqMessages,
                ]),
            fn (Response $response): string => (string) ($response->json('choices.0.message.content') ?? ''),
        );
    }

    /**
     * Execute an outbound AI provider request with uniform error logging.
     *
     * @param  callable(): Response  $request
     * @param  callable(Response): string  $extractReply
     * @return array{ok: bool, reply: ?string, provider: string, model: string, status: int}
     */
    private function executeProviderRequest(string $provider, string $model, callable $request, callable $extractReply): array
    {
        $providerKey = strtolower($provider);

        try {
            $response = $request();
        } catch (ConnectionException $exception) {
            Log::warning("{$provider} API connection failed.", [
                'message' => $exception->getMessage(),
            ]);

            return ['ok' => false, 'reply' => null, 'provider' => $providerKey, 'model' => $model, 'status' => 503];
        }

        if ($response->failed()) {
            $status = $response->status();

            Log::warning("{$provider} API request failed.", [
                'status' => $status,
                'message' => $response->json('error.message') ?? $response->body(),
            ]);

            return ['ok' => false, 'reply' => null, 'provider' => $providerKey, 'model' => $model, 'status' => $status];
        }

        return [
            'ok' => true,
            'reply' => $extractReply($response),
            'provider' => $providerKey,
            'model' => $model,
            'status' => 200,
        ];
    }

    /**
     * @param  array{ok: bool, reply: ?string, provider: string, model: string, status: int}  $result
     */
    private function successResponse(array $result): JsonResponse
    {
        return response()->json([
            'reply' => $result['reply'] ?? '',
            'provider' => $result['provider'],
            'model' => $result['model'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function errorResponse(int $status): JsonResponse
    {
        if ($status === 429) {
            return response()->json([
                'error' => 'The AI assistant is currently busy or has reached its usage limit. Please try again later.',
            ], 429);
        }

        if ($status >= 500) {
            return response()->json([
                'error' => 'The AI service is experiencing high traffic. Please try again shortly.',
            ], 503);
        }

        return response()->json([
            'error' => 'Unable to process your request right now. Please try again later.',
        ], 500);
    }

    public function context(): JsonResponse
    {
        return response()->json([
            'context' => explode("\n", str_replace(["\r\n", "\r"], "\n", $this->buildCvContext())),
        ]);
    }

    private function buildCvContext(): string
    {
        return Cache::remember('chatbot.cv_context', now()->addMinutes(30), function (): string {
            $profile = Profile::query()->first();

            if (! $profile) {
                return 'No profile data is currently available.';
            }

            $skills = Skill::query()
                ->orderBy('display_order')
                ->pluck('name')
                ->implode(', ');

            $setups = Setup::query()
                ->get()
                ->map(function (Setup $stp): string {
                    $reason = $this->flattenMultilingual($stp->reason);

                    return "- {$stp->name} ({$stp->category}): {$stp->description}".($reason !== '' ? " | Reason: {$reason}" : '');
                })
                ->implode("\n");

            $certificates = Certificate::query()
                ->latest('issued_date')
                ->get()
                ->map(fn (Certificate $cert): string => "- {$cert->title}"
                    .($cert->issuer ? " by {$cert->issuer}" : '')
                    .($cert->issued_date ? " ({$cert->issued_date->format('Y-m')})" : ''))
                ->implode("\n");

            $testimonials = Testimonial::query()
                ->latest()
                ->take(10)
                ->get()
                ->map(fn (Testimonial $testimonial): string => "- {$testimonial->name} ({$testimonial->role}): \"{$testimonial->content}\"")
                ->implode("\n");

            $socials = Social::query()
                ->get()
                ->map(fn (Social $social): string => "- {$social->name}: {$social->url}")
                ->implode("\n");

            $expertises = Expertise::query()
                ->get()
                ->map(function (Expertise $expertise): string {
                    $description = $this->flattenMultilingual($expertise->description);

                    return "- {$expertise->title}".($description !== '' ? ": {$description}" : '');
                })
                ->implode("\n");

            $hobbies = Hobby::query()
                ->pluck('name')
                ->implode(', ');

            $educations = Education::query()
                ->latest('start_period')
                ->get()
                ->map(fn (Education $edu): string => "- {$edu->institution} ({$edu->start_period?->format('Y-m')} - ".($edu->finish_period?->format('Y-m') ?? 'Present').')')
                ->implode("\n");

            $experiences = Experience::query()
                ->latest('start_period')
                ->get()
                ->map(fn (Experience $exp): string => "- {$exp->title} at {$exp->company} ({$exp->start_period?->format('Y-m')} - ".($exp->finish_period?->format('Y-m') ?? 'Present').')')
                ->implode("\n");

            $portfolios = Portfolio::query()
                ->latest('start_period')
                ->take(10)
                ->get()
                ->map(function (Portfolio $portfolio): string {
                    $title = $this->flattenMultilingual($portfolio->title);
                    $tech = is_array($portfolio->technology) ? implode(', ', $portfolio->technology) : '';

                    return "- {$title} ({$portfolio->category})"
                        .($tech !== '' ? " | Tech: {$tech}" : '')
                        .($portfolio->demo_url ? " | Demo: {$portfolio->demo_url}" : '');
                })
                ->implode("\n");

            $projects = Project::query()
                ->where('is_private', false)
                ->latest('start_period')
                ->take(10)
                ->get()
                ->map(function (Project $project): string {
                    $title = $this->flattenMultilingual($project->title);
                    $tech = is_array($project->technology) ? implode(', ', $project->technology) : '';

                    return "- {$title} ({$project->category})".($tech !== '' ? " | Tech: {$tech}" : '');
                })
                ->implode("\n");

            $tagline = $this->flattenMultilingual($profile->tagline);
            $bio = $this->flattenMultilingual($profile->description);

            return <<<CONTEXT
            Name: {$profile->fullname}
            Tagline: {$tagline}
            Location: {$profile->current_city}, {$profile->current_province}
            Bio: {$bio}
            Skills: {$skills}
            Expertises:
            {$expertises}
            Setups:
            {$setups}
            Education:
            {$educations}
            Experience:
            {$experiences}
            Public Portfolios:
            {$portfolios}
            Public Projects:
            {$projects}
            Certificates:
            {$certificates}
            Testimonials:
            {$testimonials}
            Socials:
            {$socials}
            Hobbies: {$hobbies}
            CONTEXT;
        });
    }

    private function flattenMultilingual(mixed $value): string
    {
        if (is_array($value)) {
            return (string) ($value['en'] ?? $value['id'] ?? reset($value) ?: '');
        }

        return (string) ($value ?? '');
    }
}
