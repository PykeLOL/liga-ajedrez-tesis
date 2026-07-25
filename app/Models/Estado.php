<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estados';

    public const ACTIVO = 1;
    public const INACTIVO = 2;
    public const PENDIENTE = 3;
    public const RECHAZADO = 4;

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;
}
