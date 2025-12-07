<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadisticaDeportista extends Model
{
    use HasFactory;

    protected $table = 'estadisticas_deportista';

    protected $fillable = [
        'deportista_id',
        'partidas_jugadas',
        'partidas_ganadas',
        'partidas_perdidas',
        'partidas_tablas',
        'rendimiento',
        'mejor_elo_clasico_vencido',
        'mejor_elo_rapido_vencido',
        'mejor_elo_blitz_vencido',
        'fecha_actualizacion',
    ];

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
