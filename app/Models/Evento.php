<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'liga_id',
        'nombre',
        'descripcion',
        'tipo_evento_id',
        'lugar',
        'direccion',
        'url_mapa',
        'fecha_inicio',
        'fecha_fin',
        'estado_evento_id',
        'de_pago',
        'organizador_nombre',
        'organizador_contacto',
        'publicado',
        'es_oficial',
        'max_participantes',
    ];

    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function estadoEvento()
    {
        return $this->belongsTo(EstadoEvento::class, 'estado_evento_id');
    }

    public function organizadores()
    {
        return $this->hasMany(EventoOrganizador::class);
    }

    public function media()
    {
        return $this->hasMany(EventoMedia::class);
    }

    public function documentos()
    {
        return $this->hasMany(EventoDocumento::class);
    }

    public function categorias()
    {
        return $this->hasMany(EventoCategoria::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class);
    }
}
