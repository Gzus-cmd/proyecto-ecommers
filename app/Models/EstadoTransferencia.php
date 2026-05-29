<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoTransferencia extends Model
{
    protected $table = 'central_estados_transferencia';

    protected $fillable = ['nombre'];
}