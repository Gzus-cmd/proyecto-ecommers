<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'central_sedes';

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}