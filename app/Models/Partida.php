<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;

    protected $table = 'partidas';

    protected $fillable = [
        'evento_categoria_id',
        'deportista_blancas_id',
        'deportista_negras_id',
        'ganador_id',
        'ritmo_id',
        'apertura_id',
        'fecha',
        'resultado',
    ];

    public $timestamps = true;

    public function eventoCategoria()
    {
        return $this->belongsTo(EventoCategoria::class, 'evento_categoria_id');
    }

    public function blancas()
    {
        return $this->belongsTo(Deportista::class, 'deportista_blancas_id');
    }

    public function negras()
    {
        return $this->belongsTo(Deportista::class, 'deportista_negras_id');
    }

    public function ganador()
    {
        return $this->belongsTo(Deportista::class, 'ganador_id');
    }

    public function ritmo()
    {
        return $this->belongsTo(Ritmo::class);
    }

    public function apertura()
    {
        return $this->belongsTo(Apertura::class);
    }
}
