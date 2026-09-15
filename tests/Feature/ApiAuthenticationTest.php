<?php

describe('API authentication', function () {
    it('returns JSON instead of redirecting unauthenticated API requests', function () {
        $response = $this->get('/api/users');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    });

    it('recognizes the nested my-children endpoint', function () {
        $response = $this->get('/api/my-profile/my-children');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    });
});
