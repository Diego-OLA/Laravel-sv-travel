<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospedaje extends Model
{
    use HasFactory ;
    protected $fillable=['descripcion','direccion','precio','cantidad_huespedes','checkin','checkout','user_id'];
    public function user(){
        return $this->belongsTo(User::class);   
    }

    public function imagenes(){
        return $this->hasMany(Imagen::class);
    }
    public function amenidadHospedajes(){
        return $this->hasMany(AmenidadHospedaje::class);

    }
    
}
