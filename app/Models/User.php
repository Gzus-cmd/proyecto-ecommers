<?php

namespace App\Models;


use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles; 

#[Fillable([
    'name', 
    'email', 
    'password',
    'sede_id', 
    'activo'
])]
#[Hidden([
    'password', 
    'two_factor_secret', 
    'two_factor_recovery_codes', 
    'remember_token'
])]
class User extends Authenticatable
{
    
    use HasFactory, 
        Notifiable, 
        TwoFactorAuthenticatable, 
        HasRoles; 

    /**
     * Los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'activo' => 'boolean',
        ];
    }


    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }


    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador General');
    }
}