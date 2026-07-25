<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoAsistencia extends Model
{
    use HasFactory;

    protected $table = 'estados_asistencia';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    public const PENDIENTE = 'Pendiente';
    public const ASISTIO = 'Asistió';
    public const NO_ASISTIO = 'No Asistió';
    public const EXCUSADO = 'Excusado';

    public function asistencias()
    {
        return $this->hasMany(EntrenamientoAsistencia::class, 'estado_asistencia_id');
    }
}
