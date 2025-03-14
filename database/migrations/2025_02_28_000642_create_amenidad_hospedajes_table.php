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
        Schema::create('amenidad_hospedajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('hospedaje_id');
            $table->foreign('hospedaje_id')->references('id')->on('hospedajes');
            $table->unsignedBigInteger('amenidad_id');
            $table->foreign('amenidad_id')->references('id')->on('amenidades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenidad_hospedajes');
    }
};
