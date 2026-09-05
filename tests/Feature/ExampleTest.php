<?php

it('returns a successful redirect to login for guests', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
