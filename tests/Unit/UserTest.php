<?php

use App\Models\User;
use Carbon\CarbonImmutable;

// Activamos el entorno de Laravel para las validaciones de autenticación
uses(Tests\TestCase::class);

test('el modelo user oculta correctamente sus atributos sensibles', function () {
    // 1. Arrange: Instanciamos un usuario con datos que deberían ser secretos
    $user = new User([
        'name' => 'Admin Farmacia',
        'email' => 'admin@farmacia.com',
        'password' => 'secreto123',
        'two_factor_secret' => 'CLAVE_SECRETA_2FA'
    ]);

    // 2. Act: Convertimos el modelo a un array
    $arrayUsuario = $user->toArray();

    // 3. Assert: Comprobamos que la información sensible NO se filtre
    expect($arrayUsuario)->toHaveKey('name')
        ->and($arrayUsuario)->toHaveKey('email')
        ->and($arrayUsuario)->not->toHaveKey('password')
        ->and($arrayUsuario)->not->toHaveKey('two_factor_secret');
});

test('el modelo user aplica los casts de fecha correctos para la seguridad', function () {
    // 1. Arrange: Creamos el usuario vacío
    $user = new User();
    
    // Usamos forceFill para obligar a Laravel a reconocer y mutar los atributos en frío
    $user->forceFill([
        'email_verified_at' => '2026-06-09 12:00:00',
        'two_factor_confirmed_at' => '2026-06-09 13:00:00',
    ]);

    // 2. Act & Assert: Ahora sí, Laravel procesará los campos a través del método casts()
    expect($user->email_verified_at)->toBeInstanceOf(CarbonImmutable::class)
        ->and($user->two_factor_confirmed_at)->toBeInstanceOf(CarbonImmutable::class);
});