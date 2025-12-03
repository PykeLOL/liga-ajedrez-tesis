<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadCertificacion extends Model
{
    use HasFactory;

    protected $table = 'entidades_certificacion';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = true;

    public function certificaciones()
    {
        return $this->hasMany(CertificacionEntrenador::class, 'entidad_id');
    }
}
