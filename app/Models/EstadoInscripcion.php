<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'estados_inscripcion';

    protected $fillable = ['nombre'];

    public $timestamps = true;

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class);
    }
}
