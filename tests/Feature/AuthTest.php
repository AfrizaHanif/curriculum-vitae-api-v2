<?php

/** @var TestCase $this */

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

test('user can log in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
    ]);
    Profile::factory()->for($user)->create();

    $response = $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'password123',
        'device_name' => 'mobile-app',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'profile',
            ],
        ])
        ->assertJsonPath('status', 'success');

    expect($user->fresh()->tokens)->toHaveCount(1)
        ->and($user->fresh()->tokens->first()->name)->toBe('mobile-app');
});

test('login replaces older token for the same device name', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
    ]);
    Profile::factory()->for($user)->create();

    // Create initial token
    $user->createToken('my-phone');
    expect($user->fresh()->tokens)->toHaveCount(1);

    // Login with same device name
    $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'password123',
        'device_name' => 'my-phone',
    ])->assertOk();

    // Still only 1 token because older was pruned
    expect($user->fresh()->tokens)->toHaveCount(1);
});

test('login fails with invalid password or non-existent email', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Wrong password
    $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-pass',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('message', 'The provided credentials do not match our records.')
        ->assertJsonValidationErrors(['auth']);

    // Non-existent email
    $this->postJson('/api/login', [
        'email' => 'ghost@example.com',
        'password' => 'password123',
    ])
        ->assertUnauthorized()
        ->assertJsonValidationErrors(['auth']);
});

test('login validation fails when required fields are missing', function () {
    $this->postJson('/api/login', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('login endpoint is rate limited after 6 attempts', function () {
    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/api/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    $this->postJson('/api/login', [
        'email' => 'ratelimit@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

test('authenticated user can log out and revoke token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token');

    $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
        ->postJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'message' => 'Logged out successfully. Token revoked.',
        ]);

    expect($user->fresh()->tokens)->toHaveCount(0);

    // Clear resolved guard user from memory to test token lookup against database
    $this->app['auth']->forgetGuards();

    // Using the revoked token should now fail
    $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
        ->postJson('/api/logout')
        ->assertUnauthorized();
});

test('guest cannot call logout endpoint', function () {
    $this->postJson('/api/logout')->assertUnauthorized();
});
