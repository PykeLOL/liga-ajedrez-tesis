<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoPosicion extends Model
{
    use HasFactory;

    protected $table = 'evento_posiciones';

    protected $fillable = [
        'evento_categoria_id',
        'deportista_id',
        'puesto',
        'puntos',
        'desempate',
    ];

    public $timestamps = true;

    public function categoria()
    {
        return $this->belongsTo(EventoCategoria::class, 'evento_categoria_id');
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
