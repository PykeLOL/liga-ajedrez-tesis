<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

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

    public function tienePermiso($nombrePermiso)
    {
        if ($this->permisos->contains('nombre', $nombrePermiso)) {
            return true;
        }
        if ($this->rol && $this->rol->permisos->contains('nombre', $nombrePermiso)) {
            return true;
        }
        return false;
    }

    public function getAllPermisos()
    {
        if ($this->relationLoaded('rol') || $this->rol) {
            $rolePermissions = $this->rol->permisos ?? collect();
        } else {
            $rolePermissions = collect();
        }
        $userPermissions = $this->permisos ?? collect();
        return $rolePermissions->merge($userPermissions)->unique('id');
    }
}
