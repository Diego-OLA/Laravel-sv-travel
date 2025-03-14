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
        Schema::create('hospedajes', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion',150)->nullable(false);
            $table->string('direccion',100)->nullable(false);
            $table->decimal('precio',6,2)->nullable(false);
            $table->integer('cantidad_huespedes')->nullable(false);
            $table->time('checkin')->nullable(false);
            $table->time('checkout')->nullable(false);
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospedajes');
    }
};
