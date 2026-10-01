<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Compartido extends Model
{
    protected $table = 'archivos_compartidos';
    protected $primaryKey = 'id_comparticion';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;
    protected $guarded = [];
    public function archivo() { return $this->belongsTo(Archivo::class, 'id_archivo', 'id_archivo'); }
    public function destino() { return $this->belongsTo(Usuario::class, 'id_usuario_destino', 'id_usuario'); }
}
