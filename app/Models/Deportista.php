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
        'genero',
        'nacionalidad',
        'tipo_identificacion',
        'numero_identificacion',
        'elo_nacional',
        'elo_internacional',
        'fide_id',
        'titulo',
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
}
