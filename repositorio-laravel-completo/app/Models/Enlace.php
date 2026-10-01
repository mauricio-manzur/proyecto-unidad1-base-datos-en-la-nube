<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Enlace extends Model
{
    protected $table = 'enlaces_publicos';
    protected $primaryKey = 'id_enlace';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = ['fecha_expiracion' => 'datetime'];
    public function archivo() { return $this->belongsTo(Archivo::class, 'id_archivo', 'id_archivo'); }
}
