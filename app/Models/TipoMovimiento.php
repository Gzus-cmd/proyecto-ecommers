<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table = 'central_tipos_movimiento';
    protected $fillable = ['nombre'];

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'tipo_movimiento_id');
    }
}