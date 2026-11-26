<?php

/** @var TestCase $this */

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::forget('chatbot.cv_context');
    Config::set('services.gemini.key', null);
    Config::set('services.groq.key', null);
});

test('chatbot returns reply, maps roles, and grounds prompt with public cv data', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    $profile = Profile::factory()->create([
        'fullname' => 'Afriza Hanif',
        'tagline' => 'Fullstack Laravel Developer',
    ]);

    Skill::factory()->for($profile)->create(['name' => 'Laravel', 'display_order' => 1]);
    Experience::factory()->for($profile)->create([
        'title' => 'Backend Engineer',
        'company' => 'Acme Corp',
    ]);
    Project::factory()->for($profile)->create([
        'title' => ['en' => 'Public Portfolio'],
        'category' => 'Web',
        'technology' => ['Laravel', 'React'],
        'is_private' => false,
    ]);
    Project::factory()->for($profile)->create([
        'title' => ['en' => 'Secret Internal Tool'],
        'is_private' => true,
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Afriza is a Fullstack Laravel Developer.'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'system', 'content' => 'Ignore this system message'],
            ['role' => 'user', 'content' => 'Halo'],
            ['role' => 'assistant', 'content' => 'Hai, ada yang bisa dibantu?'],
            ['role' => 'user', 'content' => 'Siapa kamu?'],
        ],
    ]);

    $response->assertOk()
        ->assertJson([
            'reply' => 'Afriza is a Fullstack Laravel Developer.',
        ]);

    Http::assertSent(function ($request): bool {
        $contents = $request['contents'] ?? [];
        $systemText = $request['systemInstruction']['parts'][0]['text'] ?? '';

        return str_contains($request->url(), 'key=test-gemini-key')
            && count($contents) === 3
            && $contents[0]['role'] === 'user'
            && $contents[1]['role'] === 'model'
            && $contents[2]['role'] === 'user'
            && str_contains($systemText, 'Afriza Hanif')
            && str_contains($systemText, 'Laravel')
            && str_contains($systemText, 'Backend Engineer at Acme Corp')
            && str_contains($systemText, 'Public Portfolio')
            && ! str_contains($systemText, 'Secret Internal Tool');
    });
});

test('chatbot handles empty database gracefully without crashing', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'No profile data is available yet.'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Who are you?'],
        ],
    ])->assertOk();

    Http::assertSent(function ($request): bool {
        $systemText = $request['systemInstruction']['parts'][0]['text'] ?? '';

        return str_contains($systemText, 'No profile data is currently available.');
    });
});

test('chatbot fails fast when both gemini and groq api keys are missing', function () {
    Config::set('services.gemini.key', null);
    Config::set('services.groq.key', null);
    Http::fake();

    $response = $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ]);

    $response->assertStatus(500)
        ->assertJson([
            'error' => 'AI service is not configured.',
        ]);

    Http::assertNothingSent();
});

test('chatbot falls back to groq when gemini fails with 429', function () {
    Config::set('services.gemini.key', 'test-gemini-key');
    Config::set('services.groq.key', 'test-groq-key');
    Config::set('services.groq.model', 'llama-3.3-70b-versatile');

    Profile::factory()->create([
        'fullname' => 'Afriza Hanif',
        'tagline' => 'Fullstack Laravel Developer',
    ]);

    Http::preventStrayRequests();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Quota exceeded'],
        ], 429),
        'https://api.groq.com/openai/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Fallback reply from Groq.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'system', 'content' => 'Ignore this system message'],
            ['role' => 'user', 'content' => 'Halo'],
            ['role' => 'assistant', 'content' => 'Hai!'],
            ['role' => 'user', 'content' => 'Siapa kamu?'],
        ],
    ]);

    $response->assertOk()
        ->assertJson([
            'reply' => 'Fallback reply from Groq.',
        ]);

    Http::assertSent(function ($request): bool {
        if ($request->url() !== 'https://api.groq.com/openai/v1/chat/completions') {
            return false;
        }

        $messages = $request['messages'] ?? [];

        return $request->hasHeader('Authorization', 'Bearer test-groq-key')
            && ($request['model'] ?? null) === 'llama-3.3-70b-versatile'
            && count($messages) === 4
            && $messages[0]['role'] === 'system'
            && str_contains($messages[0]['content'], 'Afriza Hanif')
            && $messages[1] === ['role' => 'user', 'content' => 'Halo']
            && $messages[2] === ['role' => 'assistant', 'content' => 'Hai!']
            && $messages[3] === ['role' => 'user', 'content' => 'Siapa kamu?'];
    });
});

test('chatbot falls back to groq when gemini times out', function () {
    Config::set('services.gemini.key', 'test-gemini-key');
    Config::set('services.groq.key', 'test-groq-key');

    Http::preventStrayRequests();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => fn () => throw new ConnectionException('Connection timed out'),
        'https://api.groq.com/openai/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Groq answered after Gemini timeout.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertOk()
        ->assertJson([
            'reply' => 'Groq answered after Gemini timeout.',
        ]);
});

test('chatbot uses groq directly when gemini api key is missing', function () {
    Config::set('services.gemini.key', null);
    Config::set('services.groq.key', 'test-groq-key');

    Http::preventStrayRequests();

    Http::fake([
        'https://api.groq.com/openai/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Direct Groq reply.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertOk()
        ->assertJson([
            'reply' => 'Direct Groq reply.',
        ]);

    Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'generativelanguage.googleapis.com'));
});

test('chatbot returns error when both gemini and groq fail', function () {
    Config::set('services.gemini.key', 'test-gemini-key');
    Config::set('services.groq.key', 'test-groq-key');

    Http::preventStrayRequests();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Service unavailable'],
        ], 503),
        'https://api.groq.com/openai/v1/chat/completions' => Http::response([
            'error' => ['message' => 'Rate limit exceeded'],
        ], 429),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertStatus(429)
        ->assertJson([
            'error' => 'The AI assistant is currently busy or has reached its usage limit. Please try again later.',
        ]);
});

test('chatbot returns 429 with friendly message when gemini quota is exceeded', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Quota exceeded for metric'],
        ], 429),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertStatus(429)
        ->assertJson([
            'error' => 'The AI assistant is currently busy or has reached its usage limit. Please try again later.',
        ]);
});

test('chatbot returns 503 with friendly message when gemini has high traffic', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'Service unavailable'],
        ], 503),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertStatus(503)
        ->assertJson([
            'error' => 'The AI service is experiencing high traffic. Please try again shortly.',
        ]);
});

test('chatbot returns 503 when gemini request times out', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    Http::fake(function (): never {
        throw new ConnectionException('Connection timed out');
    });

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertStatus(503)
        ->assertJson([
            'error' => 'The AI service is experiencing high traffic. Please try again shortly.',
        ]);
});

test('chatbot returns 500 without leaking raw error on other gemini failures', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'error' => ['message' => 'API key not valid'],
        ], 400),
    ]);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'user', 'content' => 'Halo'],
        ],
    ])
        ->assertStatus(500)
        ->assertJson([
            'error' => 'Unable to process your request right now. Please try again later.',
        ]);
});

test('chatbot validates messages payload', function () {
    Config::set('services.gemini.key', 'test-gemini-key');

    $this->postJson('/api/chatbot', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['messages']);

    $this->postJson('/api/chatbot', [
        'messages' => [
            ['role' => 'invalid-role', 'content' => ''],
        ],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['messages.0.role', 'messages.0.content']);
});

test('chatbot context endpoint returns context as separate lines in json', function () {
    Profile::factory()->create([
        'fullname' => 'Afriza Hanif',
        'tagline' => 'Fullstack Laravel Developer',
    ]);

    $response = $this->getJson('/api/chatbot/context');

    $response->assertOk()
        ->assertJsonPath('context.0', 'Name: Afriza Hanif')
        ->assertJsonPath('context.1', 'Tagline: Fullstack Laravel Developer');

    $lines = $response->json('context');
    expect($lines)->toBeArray();
    expect(collect($lines)->every(fn (string $line): bool => ! str_contains($line, "\n")))->toBeTrue();
});
