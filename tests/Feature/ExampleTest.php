<?php

it('anasayfa ok', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
