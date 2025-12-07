<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAccion extends Model
{
    use HasFactory;

    protected $table = 'tipo_accion';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = true;

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'tipo_accion_id');
    }
}
