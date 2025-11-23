<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deportista extends Model
{
    use HasFactory;

    protected $table = 'deportistas';

    protected $fillable = [
        'usuario_id',
        'club_id',
        'categoria_id',
        'fecha_nacimiento',
        'genero_id',
        'nacionalidad_id',
        'tipo_identificacion_id',
        'numero_identificacion',
        'elo_nacional',
        'elo_internacional',
        'fide_id',
        'titulo_id',
        'estado',
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }

    public function tipoIdentificacion()
    {
        return $this->belongsTo(TipoIdentificacion::class, 'tipo_identificacion_id');
    }

    public function titulo()
    {
        return $this->belongsTo(Titulo::class, 'titulo_id');
    }
}
