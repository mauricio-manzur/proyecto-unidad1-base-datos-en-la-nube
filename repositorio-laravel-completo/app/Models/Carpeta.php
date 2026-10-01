<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Carpeta extends Model
{
    protected $table = 'carpetas';
    protected $primaryKey = 'id_carpeta';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    protected $guarded = [];

    public function padre() { return $this->belongsTo(Carpeta::class, 'id_carpeta_padre', 'id_carpeta'); }

    /** Ids de la carpeta y todas sus subcarpetas (recorrido por niveles, padres primero). */
    public function descendientesIds(): array
    {
        $ids = [$this->id_carpeta]; $nivel = $ids;
        while ($nivel) {
            $nivel = static::whereIn('id_carpeta_padre', $nivel)->pluck('id_carpeta')->all();
            $ids = array_merge($ids, $nivel);
        }
        return $ids;
    }
}
