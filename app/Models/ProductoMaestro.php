<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class ProductoMaestro extends Model
{
    use HasFactory;
 
    protected $table = 'central_productos_maestro';
 
    protected $fillable = [
        'sku',
        'nombre_comercial',
        'nombre_generico',
        'descripcion',
        'categoria_id',
        'laboratorio_id',
        'proveedor_id',
        'requiere_receta',
        'registro_sanitario',
        'concentracion',
        'forma_farmaceutica',
        'unidad_medida',
        'stock_minimo',
        'activo',
    ];
 
    protected $casts = [
        'requiere_receta' => 'boolean',
        'activo'          => 'boolean',
        'stock_minimo'    => 'integer',
    ];
 
    // ── Relaciones ──────────────────────────────────────────
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
 
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    }
 
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
 
    public function lotes()
    {
        return $this->hasMany(Lote::class, 'producto_id');
    }
}