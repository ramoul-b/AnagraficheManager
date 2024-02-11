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
        Schema::create('anagrafiche_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anagrafica_id');
            $table->unsignedBigInteger('attributes_id');
            $table->string('value', 255);
            $table->foreign('anagrafica_id')->references('id')->on('anagrafiche');
            $table->foreign('attributes_id')->references('id')->on('attributes');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anagrafiche_values');
    }
};


