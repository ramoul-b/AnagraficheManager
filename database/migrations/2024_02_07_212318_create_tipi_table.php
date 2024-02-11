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
        Schema::create('tipi', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->text('descrizione');
            $table->boolean('has_partita_iva');
            $table->boolean('required_partita_iva');
            $table->boolean('has_codice_fiscale');
            $table->boolean('required_codice_fiscale');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo');
    }
};
