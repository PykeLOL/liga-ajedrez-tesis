<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'estados_entrenamiento';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    public const PROGRAMADO = 'Programado';
    public const EN_CURSO = 'En curso';
    public const FINALIZADO = 'Finalizado';
    public const CANCELADO = 'Cancelado';

    public function entrenamientos()
    {
        return $this->hasMany(Entrenamiento::class);
    }
}
