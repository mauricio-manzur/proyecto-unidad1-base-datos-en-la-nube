<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = 'id_archivo';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    protected $guarded = [];

    public function carpeta() { return $this->belongsTo(Carpeta::class, 'id_carpeta', 'id_carpeta'); }
    public function propietario() { return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario'); }
    public function comparticiones() { return $this->hasMany(Compartido::class, 'id_archivo', 'id_archivo'); }
    public function enlaces() { return $this->hasMany(Enlace::class, 'id_archivo', 'id_archivo'); }

    /** ¿Puede este usuario ver el archivo? (propiedad directa o compartición) */
    public function permisoPara($uid): ?string
    {
        if ($this->id_usuario == $uid) return 'propietario';
        return Compartido::where('id_archivo', $this->id_archivo)
            ->where('id_usuario_destino', $uid)->value('permiso');
    }
}
