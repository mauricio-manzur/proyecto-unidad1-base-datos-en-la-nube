<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';
    protected $guarded = [];
    protected $hidden = ['password_hash'];
    public function getAuthPassword() { return $this->password_hash; }
    public function getAuthPasswordName() { return 'password_hash'; }
    public function getRememberTokenName() { return ''; }
}
