<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificacionEntrenador extends Model
{
    use HasFactory;

    protected $table = 'certificaciones_entrenador';

    protected $fillable = [
        'nombre',
        'entidad_id',
        'descripcion'
    ];

    public $timestamps = true;

    public function entrenadores()
    {
        return $this->hasMany(Entrenador::class, 'certificacion_entrenador_id');
    }

    public function entidad()
    {
        return $this->belongsTo(EntidadCertificacion::class, 'entidad_id');
    }
}
