<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'nama_tenant' => 'Toko Saya',
        'subdomain' => 'tokosaya',
        'jenis_usaha' => 'Retail',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect('http://tokosaya.localhost/dashboard');

    $this->assertAuthenticated();
});