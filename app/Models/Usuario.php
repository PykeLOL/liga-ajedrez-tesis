<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'email',
        'telefono',
        'contraseña',
        'imagen_path',
        'estado',
        'rol_id',
        'imagen_path'
    ];

    protected $hidden = [
        'contraseña'
    ];

    public $timestamps = true;

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'usuarios_permisos', 'usuario_id', 'permiso_id')->withTimestamps();
    }
}
