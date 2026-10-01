<?php

test('api root returns successful json status', function () {
    $response = $this->getJson('/api');

    $response->assertOk()
        ->assertJsonStructure([
            'name',
            'status',
            'message',
            'version',
            'timestamp',
        ])
        ->assertJson([
            'status' => 'ok',
        ]);
});
