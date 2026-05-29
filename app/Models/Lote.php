<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Lote extends Model
{
    use HasFactory;
 
    protected $table = 'central_lotes';
 
    protected $fillable = [
        'producto_id',
        'numero_lote',
        'fecha_fabricacion',
        'fecha_ingreso',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_actual',
        'costo_unitario',
        'estado',
    ];
 
    protected $casts = [
        'fecha_fabricacion' => 'date',
        'fecha_ingreso'     => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad_inicial'  => 'integer',
        'cantidad_actual'   => 'integer',
        'costo_unitario'    => 'decimal:2',
    ];
 
    // ── Relaciones ──────────────────────────────────────────
    public function producto()
    {
        return $this->belongsTo(ProductoMaestro::class, 'producto_id');
    }
}