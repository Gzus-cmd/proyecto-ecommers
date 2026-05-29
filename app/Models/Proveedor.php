<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Proveedor extends Model
{
    use HasFactory;
 
    protected $table = 'central_proveedores';
 
    protected $fillable = [
        'razon_social',
        'ruc',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'activo',
    ];
 
    protected $casts = [
        'activo' => 'boolean',
    ];
 
    // ── Relaciones ──────────────────────────────────────────
    public function productos()
    {
        return $this->hasMany(ProductoMaestro::class, 'proveedor_id');
    }
}
 