<?php

test('homepage endpoint responds successfully', function () {
    $response = $this->getJson('/api/homepage');

    $response->assertOk();
    $response->assertJsonStructure([
        'success',
        'message',
        'data',
    ]);
});
