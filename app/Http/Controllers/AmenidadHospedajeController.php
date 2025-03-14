<?php

namespace App\Http\Controllers;

use App\Models\AmenidadHospedaje;
use Illuminate\Http\Request;

class AmenidadHospedajeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            return response()->json(AmenidadHospedaje::with("hospedaje","amenidad")->get());
    
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
            $registro = AmenidadHospedaje::where("amenidad_id",$request["amenidad"]["id"] ?? null)->where("hospedaje_id",$request["hospedaje"]["id"])->first();
            if($registro){
                return response()->json(['Message'=>"Ya existe esta amenidad en el hospedaje"],409);
            }
           
            $amenidadHos = new AmenidadHospedaje();
            $amenidadHos->hospedaje_id = $request["hospedaje"]["id"];
            $amenidadHos->amenidad_id = $request["amenidad"]["id"];
           // return $amenidad;
            $amenidadHos->save();
            $amenidadHosAgregada = $this->show($amenidadHos->id);
            return response()->json(["message"=>"La amenidad: ". " fue registrada con exito"],201);


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
            return response()->json(AmenidadHospedaje::findOrFail($id));

        }catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AmenidadHospedaje $amenidadHospedaje)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $registro = AmenidadHospedaje::find($id);
            if(!$registro){
                return response()->json(["message"=>"Amenidad no encontrada"],404);
            }
            $registro->hospedaje_id = $request["hospedaje"]["id"];
            $registro->amenidad_id = $request["amenidad"]["id"];
            $registro->update();
            return response()->json(["message"=>"la amenidad con id:".$registro->id." fue actualizada"],200);

        }catch(Exception $e){
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AmenidadHospedaje $amenidadHospedaje)
    {
        //
    }
}
