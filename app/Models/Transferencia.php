<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'central_transferencias';

    protected $fillable = [
        'sede_destino_id',
        'fecha_envio',
        'fecha_recepcion',
        'estado',
        'observaciones'
    ];

    public function sedeDestino()
    {
        return $this->belongsTo(Sede::class, 'sede_destino_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTransferencia::class, 'transferencia_id');
    }

    // Relación polimórfica inversa: obtener todos los movimientos de esta transferencia
    public function movimientos()
    {
        return $this->morphMany(MovimientoInventario::class, 'movimentable');
    }
}