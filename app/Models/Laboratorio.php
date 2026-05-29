<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class Laboratorio extends Model
{
    use HasFactory;
 
    protected $table = 'central_laboratorios';
 
    protected $fillable = [
        'nombre',
        'pais',
    ];
 
    // ── Relaciones ──────────────────────────────────────────
    public function productos()
    {
        return $this->hasMany(ProductoMaestro::class, 'laboratorio_id');
    }
}
 