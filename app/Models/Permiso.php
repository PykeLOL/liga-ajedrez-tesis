<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_accion_id',
        'modulo_id'
    ];

    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'roles_permisos', 'permiso_id', 'rol_id')->withTimestamps();
    }

    public function usuarios()
    {
        return $this->belongsToMany(Rol::class, 'usuarios_permisos', 'permiso_id', 'usuario_id')->withTimestamps();
    }
}
