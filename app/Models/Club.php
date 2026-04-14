<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $table = 'clubes';

    protected $fillable = [
        'liga_id',
        'nombre',
        'descripcion',
        'ubicacion',
        'direccion',
        'url_mapa',
        'presidente_id',
        'contacto',
        'logo',
        'documento_path',
        'estado_id',
    ];

    public $timestamps = true;

    public function liga()
    {
        return $this->belongsTo(Liga::class, 'liga_id');
    }

    public function presidente()
    {
        return $this->belongsTo(Usuario::class, 'presidente_id');
    }

    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'club_id');
    }

    public function media()
    {
        return $this->hasMany(ClubMedia::class);
    }

    public function redesSociales()
    {
        return $this->hasMany(ClubRedSocial::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
