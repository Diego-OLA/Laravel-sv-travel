<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use App\Models\Imagen;
use App\Models\User;
use App\Models\Reserva;
use Illuminate\Http\Request;

class HospedajeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     try{
        return response()->json(Hospedaje::with("user","imagenes")->get());

    }catch(Exception $e){
        return response()->json(['error'=>$e->getMessage()],500);
    }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $hospedajeRequest = json_decode($request->input("hospedaje"),true);
                   
               //return $hospedajeRequest["checkin"]."La primera fecha es de check in". $hospedajeRequest["checkout"];
               $registro = Hospedaje::where("descripcion",$hospedajeRequest["descripcion"] ?? null)
               ->where("direccion",$hospedajeRequest["direccion"])
               ->where("precio",$hospedajeRequest["precio"])
               ->whereHas('user', function($query) use ($hospedajeRequest){ //whereHas es un metodo que me permite establecer
                   //un filtro entre las relaciones de los modelos producto con marca, $query representa una consulta sql 
                   //la cual no tiene ningun parametro de busqueda cuando se establece la funcion asi que dentro del whereHas estoy
                   //verificando que en productoRequest haya un id que este presente en marca pero como en el modelo no puedo acceder
                   //al id de marca pero si al nombre hago la comparacion por ahi
                   if(isset($hospedajeRequest["user"]["nombre"])){
                       $query->where("nombre",$hospedajeRequest["user"]["nombre"]);
                   }
               })->first();
               if($registro){
                   return response()->json(["message"=>"Ya existe un hospedaje con estos datos"],409);
               }

              

               //creamos una instancia de Producto y llenamos el objetos con los datos de $request
               $hospedaje = new Hospedaje();
               $hospedaje->descripcion = $hospedajeRequest["descripcion"];
               $hospedaje->direccion = $hospedajeRequest["direccion"];
               $hospedaje->precio = $hospedajeRequest["precio"];
               $hospedaje->cantidad_huespedes = $hospedajeRequest["cantidad_huespedes"];
               $hospedaje->checkin = $hospedajeRequest["checkin"];
               $hospedaje->checkout = $hospedajeRequest["checkout"];
               $hospedaje->user_id = $hospedajeRequest["user"]["id"];
               $hospedaje->save(); //guardamos en productos

             

               //verificamos si la peticion trae imágenes
               if($request->hasFile('imagenes')){
                   //recorremos la colección de imagenes para guardarlas en "imagenes"
                   foreach($request->file('imagenes') as $img){
                       //creamos un nombre único de la imagen
                       $imageName = time() . '_' . $img->getClientOriginalName();
                       //subimos el archivo de imagen a una carpeta publica del servidor
                       $img->move(public_path('images/hospedajes/'),$imageName);
                       //creamos la instancia de Imagen para luego guardar cada registro en 
                       //la tabla de imagenes
                       $image = new Imagen();
                       $image->nombre = $imageName;
                       $image->hospedaje_id = $hospedaje->id;
                       $image->save();
                   }
               }
                   $hosPersisted = $this->show($hospedaje->id);
                   return response()->json(["hospedaje"=>$hosPersisted,
                   "message"=>"Hospedaje Registrado...!"],201);
       }catch(\Exception $e){
           return response()->json(['error'=>$e->getMessage()],500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{ 

       
          return response()->json(Hospedaje::with("user","imagenes")->findOrFail($id));

        }catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }
    public function getByUserId($id)
    {
        // Buscar registros donde user_id coincida con el id proporcionado
        $registros = Hospedaje::where('user_id', $id)->get();

        // Devolver los registros en formato JSON
        return response()->json($registros);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hospedaje $hospedaje)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{

            $hospedaje = new Hospedaje;
            $hospedaje = Hospedaje::findOrFail($id);
            $hospedajeRequest = json_decode($request->input("hospedaje"),true);
            $imagen = new Imagen;
            //Eliminar Imagenes
         if($request->hasFile('imagenes')){
            foreach($hospedaje->imagenes as $img){
                $imgPath = public_path().'/images/hospedajes/'.$img->nombre;
                
                 unlink($imgPath);
                
                 $imagen->where('hospedaje_id',$hospedaje->id)->delete();
            }
          
        }
        
                $hospedaje->descripcion = $hospedajeRequest["descripcion"];
                $hospedaje->direccion = $hospedajeRequest["direccion"];
                $hospedaje->precio = $hospedajeRequest["precio"];
                $hospedaje->cantidad_huespedes = $hospedajeRequest["cantidad_huespedes"];
                $hospedaje->checkin = $hospedajeRequest["checkin"];
                $hospedaje->checkout = $hospedajeRequest["checkout"];
                $hospedaje->user_id = $hospedajeRequest["user"]["id"];
               
                $hospedaje->update();
                if($request->hasFile('imagenes')){
                    //recorremos la colección de imagenes para guardarlas en "imagenes"
                    foreach($request->file('imagenes') as $img){
                        //creamos un nombre único de la imagen
                        $imageName = time() . '_' . $img->getClientOriginalName();
                        //subimos el archivo de imagen a una carpeta publica del servidor
                        $img->move(public_path('images/hospedajes/'),$imageName);
                        //creamos la instancia de Imagen para luego guardar cada registro en 
                        //la tabla de imagenes
                        $image = new Imagen();
                        $image->nombre = $imageName;
                        $image->hospedaje_id = $hospedaje->id;
                        $image->save();
                    }
                }
                return response()->json(['Message'=>'hospedaje con id '.$id .' actualizado con exito'],200);
        } catch(Exception $e){
        return response()->json(['error'=>$e->getMessage()],500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $hospedaje = Hospedaje::findOrFail($id);
            //verificamos si hay registros del producto en la tabla 
         $registro = Reserva::where('hospedaje_id',$hospedaje->id)->first();
         if(!$registro)
         {
             //podemos eliminar el producto, eliminamos las imagenes primero 
             foreach($hospedaje->imagenes as $img){
                 $imgPath = public_path(). '/images/hospedajes/'. $img->nombre;
                  unlink($imgPath);
             
 
             }
             $hospedaje->imagenes()->delete();
             //finalmente borramos el producto
            if( $hospedaje->delete()>0){
             return response()->json(['Hospedaje borrado con exito'],200);
            }
          }else{
             return response()->json(['Message'=>'no se puede borrar el Hospedaje, porque ya se encuentra ligado a la tabla detalle orden'],409);
          }
         }catch(Exception $e){
             return response()->json(['error'=>$e->getMessage()],500);
         }
    }
}
