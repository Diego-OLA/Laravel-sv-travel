<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
    protected $fillable = ["fecha_pago","total","user_id","reserva_id"];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function reserva(){
        return $this->belongsTo(Reserva::class);
    }

}
