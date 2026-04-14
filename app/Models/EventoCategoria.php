<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoCategoria extends Model
{
    use HasFactory;

    protected $table = 'evento_categorias';

    protected $fillable = [
        'evento_id',
        'categoria_id',
        'ritmo_id',
        'cupo_maximo',
        'costo_inscripcion',
        'genero_id',
        'orden'
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ritmo()
    {
        return $this->belongsTo(Ritmo::class);
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class, 'evento_categoria_id');
    }

    public function posiciones()
    {
        return $this->hasMany(EventoPosicion::class, 'evento_categoria_id');
    }
}
