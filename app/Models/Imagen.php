<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = "imagenes";
    use HasFactory;
    protected $fillable =['nombre','hospedaje_id'];
  
  public function hospedaje(){
      return $this->belongsTo(Hospedaje::class);
  }
}
