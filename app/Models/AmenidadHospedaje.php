<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenidadHospedaje extends Model
{
    use HasFactory ;
    protected $fillable=['hospedajes_id','amenidades_id'];
    public function hospedaje(){
        return $this->belongsTo(Hospedaje::class);
    }
    public function amenidad(){
        return $this->belongsTo(Amenidad::class);

    }
}