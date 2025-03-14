<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Reserva extends Model
{
   
    use HasFactory;
    protected $fillable =['fecha_reserva','fecha_entrada','fecha_salida','noches','hospedaje_id','estado_reserva_id','user_id'];
    public function hospedaje(){
        return $this->belongsTo(Hospedaje::class);
    }   
    public function estado_reserva(){
        return $this->belongsTo(EstadoReserva::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

}
