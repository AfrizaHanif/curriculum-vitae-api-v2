<?php

test('allows requests from production origin afrizahanif.com on api root', function () {
    $response = $this->withHeaders([
        'Origin' => 'https://afrizahanif.com',
    ])->getJson('/api');

    $response->assertOk()
        ->assertHeader('Access-Control-Allow-Origin', 'https://afrizahanif.com');
});

test('allows requests from production origin afrizahanif.com on api resources', function () {
    $response = $this->withHeaders([
        'Origin' => 'https://afrizahanif.com',
    ])->getJson('/api/skills?all=true');

    $response->assertOk()
        ->assertHeader('Access-Control-Allow-Origin', 'https://afrizahanif.com');
});

test('allows requests from subdomains matching afrizahanif.com pattern', function () {
    $response = $this->withHeaders([
        'Origin' => 'https://www.afrizahanif.com',
    ])->getJson('/api/skills?all=true');

    $response->assertOk()
        ->assertHeader('Access-Control-Allow-Origin', 'https://www.afrizahanif.com');
});

test('handles preflight OPTIONS requests for allowed origin', function () {
    $response = $this->withHeaders([
        'Origin' => 'https://afrizahanif.com',
        'Access-Control-Request-Method' => 'GET',
        'Access-Control-Request-Headers' => 'Content-Type, Authorization',
    ])->options('/api/skills');

    $response->assertNoContent()
        ->assertHeader('Access-Control-Allow-Origin', 'https://afrizahanif.com')
        ->assertHeader('Access-Control-Allow-Methods', 'GET');
});

test('does not return access control allow origin header for untrusted origins', function () {
    $response = $this->withHeaders([
        'Origin' => 'https://untrusted-domain.com',
    ])->getJson('/api/skills');

    $response->assertOk()
        ->assertHeaderMissing('Access-Control-Allow-Origin');
});
