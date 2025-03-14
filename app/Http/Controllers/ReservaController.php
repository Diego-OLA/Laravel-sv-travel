<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\EstadoReserva;
use App\Models\Pago;
use App\Models\Hospedaje;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            return response()->json(Reserva::with("hospedaje")->get());
    
        }catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            
        try{
           $validData = $request->validate([
            'fecha_reserva'=> 'required|date',
            'fecha_entrada'=> 'required|date',
            'fecha_salida'=> 'required|date',
            'hospedaje.id'=> 'required|exists:hospedajes,id',
            'estado_reserva.id'=> 'required|exists:estado_reservas,id'
           ]);
          
          
               $registro = Reserva::where("fecha_reserva",$request->fecha_reserva ?? null)
               ->where("fecha_entrada",$request->fecha_entrada)
               ->where("fecha_salida",$request->fecha_salida)
               ->whereHas('hospedaje', function($query) use ($request){ //whereHas es un metodo que me permite establecer
                   //un filtro entre las relaciones de los modelos producto con marca, $query representa una consulta sql 
                   //la cual no tiene ningun parametro de busqueda cuando se establece la funcion asi que dentro del whereHas estoy
                   //verificando que en productoRequest haya un id que este presente en marca pero como en el modelo no puedo acceder
                   //al id de marca pero si al nombre hago la comparacion por ahi
                   if(isset($request->hospedaje_id->descripcion)){
                       $query->where("descripcion",$request->hospedaje_id->descripcion);
                   }
               })->first();
               if($registro){
                   return response()->json(["message"=>"Ya existe un hospedaje con estos datos"],409);
               }
               //creamos una instancia de reserva y llenamos el objetos con los datos de $request
               $fechaActual = Carbon::now()->toDateString();
               $reserva = new Reserva();
               $reserva->fecha_reserva = $fechaActual;
               $reserva->fecha_entrada = $request->fecha_entrada;
               $reserva->fecha_salida = $request->fecha_salida;
               $reserva->noches = $request->noches;
               $reserva->hospedaje_id = $request["hospedaje"]["id"];
               $reserva->estado_reserva_id = $request["estado_reserva"]["id"];
               $reserva->user_id = $request["user"]["id"];
               $reserva->save(); //guardamos en reservas
               //verificamos si la peticion trae imágenes
               
                   $resPersisted = $this->show($reserva->id);
                   return response()->json(["reserva"=>$resPersisted,
                   "message"=>"Reserva Registrada...!"],201);
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
            return response()->json(Reserva::with("hospedaje")->findOrFail($id));

        }catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    
                try{
            $reserva = Reserva::find($id);
            if(!$reserva){
                return response()->json([
                    'status'=>'Not found',
                    'message'=>'Orden no encontrada'
                ],404);
            }

        
        //verificamos el estado por $request
       
        
        $fechaActual = Carbon::now()->toDateString();
        $reserva->fecha_reserva = $fechaActual;
        $reserva->fecha_entrada = $request["fecha_entrada"];
        $reserva->fecha_salida = $request["fecha_salida"];
        $reserva->hospedaje_id = $request["hospedaje"]["id"];
        $reserva->noches = $request["noches"];
        $reserva->estado_reserva_id = $request["estado_reserva"]["id"];
        $reserva->user_id=$request["user"]["id"];

        //verificamos si el estado de la reserva es igual aprovada para hacer la simulacion del pago
        //creamos una instancia de hospedaje para sacar total
       
        
        $hospedaje = Reserva::with("hospedaje")->find($reserva->hospedaje_id);
        $precio = $hospedaje["hospedaje"]["precio"];

        if($reserva->estado_reserva_id == 1){
        $pago = new Pago();
        $pago->fecha_pago = $fechaActual;
        $pago->total = $request["noches"]*$precio;
        $pago->reserva_id = $reserva->id;
        $pago->save();
    }

        $reserva->update();
        return response()->json([
            'message'=>'La reserva fue actualizada, fecha de reserva: ' . $reserva->fecha_reserva,
        ],202);
     } catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
             }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    { 
        try{
        $reserva  = Reserva::findOrFail($id);
            if($reserva->delete()>0){
                return response()->json(['Reserva borrada con exito'],200);
            }
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
             }
    }
}
