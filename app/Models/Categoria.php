<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Categoria extends Model
{
    use HasFactory;
 
    protected $table = 'central_categorias';
 
    protected $fillable = [
        'nombre',
        'descripcion',
    ];
 
    // ── Relaciones ──────────────────────────────────────────
    public function productos()
    {
        return $this->hasMany(ProductoMaestro::class, 'categoria_id');
    }
}