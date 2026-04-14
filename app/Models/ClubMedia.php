<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMedia extends Model
{
    use HasFactory;

    protected $table = 'club_media';

    protected $fillable = [
        'club_id',
        'tipo',
        'path',
        'descripcion',
        'orden',
    ];

    public $timestamps = true;

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
