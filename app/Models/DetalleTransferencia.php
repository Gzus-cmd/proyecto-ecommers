<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTransferencia extends Model
{
    protected $table = 'central_detalle_transferencias';

    protected $fillable = [
        'transferencia_id', 
        'lote_id', 
        'cantidad'
    ];

    public function transferencia()
    {
        return $this->belongsTo(Transferencia::class, 'transferencia_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}