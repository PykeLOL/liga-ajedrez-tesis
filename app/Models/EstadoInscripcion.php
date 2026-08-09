<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'estados_inscripcion';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    public const PENDIENTE = 1;
    public const PAGADO = 2;
    public const RECHAZADO = 3;
    public const CANCELADO = 4;

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class);
    }
}
