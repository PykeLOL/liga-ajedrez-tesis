<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRedSocial extends Model
{
    use HasFactory;

    protected $table = 'club_redes_sociales';

    protected $fillable = [
        'club_id',
        'red_social_id',
        'orden',
        'url'
    ];

    public $timestamps = true;

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function redSocial()
    {
        return $this->belongsTo(RedSocial::class);
    }
}
