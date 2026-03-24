<?php

it('shows login link on landing page', function () {
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('Connexion');
});
