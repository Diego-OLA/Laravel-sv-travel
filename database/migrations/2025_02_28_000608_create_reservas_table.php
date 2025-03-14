<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_reserva')->nullable(false);
            $table->date('fecha_entrada')->nullable(false);
            $table->date('fecha_salida')->nullable(false);
            $table->integer('noches')->nullable(false);
            $table->unsignedBigInteger('hospedaje_id');
            $table->foreign('hospedaje_id')->references('id')->on('hospedajes');
            $table->unsignedBigInteger('estado_reserva_id');
            $table->foreign('estado_reserva_id')->references('id')->on('estado_reservas');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
