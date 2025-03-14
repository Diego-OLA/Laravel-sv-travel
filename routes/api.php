<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\controllers\HospedajeController;
use App\Http\controllers\ReservaController;
use App\Http\controllers\AmenidadController;
use App\Http\controllers\AmenidadHospedajeController;
use App\Http\controllers\PagoController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('hospedajes',HospedajeController::class);
Route::apiResource('reservas',ReservaController::class);
Route::apiResource('amenidades',AmenidadController::class);
Route::apiResource('pagos',PagoController::class);
Route::apiResource('amenidadHospedajes',AmenidadHospedajeController::class);
Route::get('/registros/{id}', [HospedajeController::class, 'getByUserId']);

