<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('attributes')->insert([
            ['name' => 'Nome'],
            ['name' => 'Indirizzo'],
            ['name' => 'Telefono'],
            ['name' => 'Email'],
            ['name' => 'Sito Web'],
            ['name' => 'Settore di Attività'],
            ['name' => 'Data di Fondazione'],
        ]);
    }
}
