<?php

namespace App\Http\Controllers;

use App\Models\Amenidad;
use Illuminate\Http\Request;

class AmenidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            return response()->json(Amenidad::with("amenidadHospedajes")->get());
    
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
            $registro = Amenidad::where("tipo",$request->tipo ?? null)->first();
            if($registro){
                return response()->json(['Message'=>"Ya existe esta amenidad"],409);
            }
           
            $amenidad = new Amenidad();
            $amenidad->tipo = $request->tipo;
           // return $amenidad;
            $amenidad->save();
            $amenidadAgregada = $this->show($amenidad->id);
            return response()->json(["message"=>"La amenidad: ".$amenidad->tipo. " fue registrada con exito"],201);


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
            return response()->json(Amenidad::findOrFail($id));

        }catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Amenidad $amenidad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $registro = Amenidad::find($id);
            if(!$registro){
                return response()->json(["message"=>"Orden no encontrada"],404);
            }
            $registro->tipo = $request->tipo;
            $registro->update();
            return response()->json(["message"=>"la amenidad con id:".$registro->id." fue actualizada"],200);

        }catch(Exception $e){
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amenidad $amenidad)
    {
        //
    }
}
