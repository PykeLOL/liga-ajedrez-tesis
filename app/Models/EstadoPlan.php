<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoPlan extends Model
{
    use HasFactory;

    protected $table = 'estados_plan';

    protected $fillable = ['nombre'];

    public $timestamps = false;

    public const BORRADOR = 'Borrador';
    public const ACTIVO = 'Activo';
    public const FINALIZADO = 'Finalizado';
    public const CANCELADO = 'Cancelado';

    public function planes()
    {
        return $this->hasMany(PlanEntrenamiento::class);
    }
}
