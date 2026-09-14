<?php

describe('API authentication', function () {
    it('returns JSON instead of redirecting unauthenticated API requests', function () {
        $response = $this->get('/api/users');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    });
});
