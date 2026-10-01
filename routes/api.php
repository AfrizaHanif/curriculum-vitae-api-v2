<?php

use App\Http\Controllers\APIs\AuthController;
use App\Http\Controllers\APIs\CaseStudyController;
use App\Http\Controllers\APIs\CertificateController;
use App\Http\Controllers\APIs\EducationController;
use App\Http\Controllers\APIs\ExperienceController;
use App\Http\Controllers\APIs\ExpertiseController;
use App\Http\Controllers\APIs\FeatureController;
use App\Http\Controllers\APIs\HobbyController;
use App\Http\Controllers\APIs\PortfolioController;
use App\Http\Controllers\APIs\PostController;
use App\Http\Controllers\APIs\ProfileController;
use App\Http\Controllers\APIs\ProjectController;
use App\Http\Controllers\APIs\SetupController;
use App\Http\Controllers\APIs\SkillController;
use App\Http\Controllers\APIs\SocialController;
use App\Http\Controllers\APIs\TestimonialController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): JsonResponse {
    return response()->json([
        'name' => config('app.name', 'Curriculum Vitae API'),
        'status' => 'ok',
        'message' => 'API is running smoothly.',
        'version' => '2.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function (): void {
    // Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()->loadMissing('profile')));
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('profiles', ProfileController::class)->only(['index', 'show', 'update']);

$apiResources = [
    'skills' => SkillController::class,
    'educations' => EducationController::class,
    'experiences' => ExperienceController::class,
    'expertises' => ExpertiseController::class,
    'certificates' => CertificateController::class,
    'hobbies' => HobbyController::class,
    'portfolios' => PortfolioController::class,
    'projects' => ProjectController::class,
    'posts' => PostController::class,
    'setups' => SetupController::class,
    'socials' => SocialController::class,
    'testimonials' => TestimonialController::class,
    'case-studies' => CaseStudyController::class,
    'features' => FeatureController::class,
];

foreach ($apiResources as $name => $controller) {
    Route::post("$name/{id}/restore", [$controller, 'restore'])->name("$name.restore")->withTrashed();
    Route::apiResource($name, $controller)->withTrashed(['destroy']);
}
