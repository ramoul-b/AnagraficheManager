<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipiTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipi')->insert([
            ['nome' => 'Impresa', 'descrizione' => 'Un\'entità commerciale o industriale', 'has_partita_iva' => 1, 'required_partita_iva' => 1, 'has_codice_fiscale' => 1, 'required_codice_fiscale' => 1],
            ['nome' => 'Persona', 'descrizione' => 'Un individuo, per fini non commerciali', 'has_partita_iva' => 0, 'required_partita_iva' => 0, 'has_codice_fiscale' => 1, 'required_codice_fiscale' => 1],
            ['nome' => 'Associazione', 'descrizione' => 'Un gruppo organizzato con un obiettivo comune', 'has_partita_iva' => 0, 'required_partita_iva' => 0, 'has_codice_fiscale' => 1, 'required_codice_fiscale' => 0],
            ['nome' => 'Ente Pubblico', 'descrizione' => 'Un organismo statale o amministrativo', 'has_partita_iva' => 0, 'required_partita_iva' => 0, 'has_codice_fiscale' => 1, 'required_codice_fiscale' => 1],
        ]);
    }
}
