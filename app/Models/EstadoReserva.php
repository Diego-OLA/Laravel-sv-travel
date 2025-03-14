<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EstadoReserva extends Model
{
   
    use HasFactory;
    protected $fillable =['estados'];
    public function reservas(){
        return $this->hasMany(Reserva::class);
    }   
   

}
