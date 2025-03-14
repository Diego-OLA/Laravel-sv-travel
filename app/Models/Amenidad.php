<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenidad extends Model
{
    protected $table = "amenidades";
    use HasFactory;
    protected $fillable = ["tipo"];

    public function AmenidadHospedajes(){
        return $this->hasMany(AmenidadHospedaje::class);
    }
}
