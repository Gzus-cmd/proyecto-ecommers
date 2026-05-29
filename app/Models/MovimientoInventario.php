<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'central_movimientos_inventario';

    protected $fillable = [
        'lote_id',
        'tipo_movimiento_id',
        'movimentable_id',
        'movimentable_type',
        'cantidad',
        'stock_antes',
        'stock_despues',
        'usuario_id',
        'fecha_movimiento',
        'observacion'
    ];

    // Relación polimórfica: permite que el movimiento apunte a una Transferencia, Venta, etc.
    public function movimentable()
    {
        return $this->morphTo();
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoMovimiento::class, 'tipo_movimiento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}