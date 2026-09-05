<?php

test('registration is disabled for internal ecosystem', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});
