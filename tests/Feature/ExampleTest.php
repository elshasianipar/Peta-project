<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('renders the login page without a Vite manifest', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
