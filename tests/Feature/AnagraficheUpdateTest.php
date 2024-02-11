<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Anagrafiche;
use App\Models\Tipi;
use App\Models\Attribute;
use App\Models\AnagraficheValue;

class AnagraficheUpdateTest extends TestCase
{
   // use RefreshDatabase;

    /** @test */
    public function an_anagrafica_can_be_updated()
    {
        // Arrange
        $tipi = Tipi::create([
            'nome' => 'Impresa',
            'descrizione' => 'Descrizione del tipo Impresa',
            'has_partita_iva' => 1,
            'required_partita_iva' => 1,
            'has_codice_fiscale' => 1,
            'required_codice_fiscale' => 1,
        ]);

        $anagrafica = Anagrafiche::create([
            'partita_iva' => '12345678901',
            'codice_fiscale' => '12345678901',
            'unique_code' => 'UNIQCODE123',
            'tipo_id' => $tipi->id,
        ]);

        $attribute = Attribute::create(['name' => 'Email']);

        AnagraficheValue::create([
            'anagrafica_id' => $anagrafica->id,
            'attributes_id' => $attribute->id,
            'value' => 'old@example.com',
        ]);

        $updatedData = [
            'partita_iva' => '98765432109',
            'codice_fiscale' => '98765432109',
            'unique_code' => 'NEWUNIQCODE123',
            'tipo_id' => $tipi->id,
            'attributes' => [
                ['attribute_id' => $attribute->id, 'value' => 'new@example.com']
            ],
        ];

        // Act
        $response = $this->putJson("/api/anagrafiche/{$anagrafica->id}", $updatedData);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('anagrafiche', [
            'id' => $anagrafica->id,
            'partita_iva' => '98765432109',
            'codice_fiscale' => '98765432109',
            'unique_code' => 'NEWUNIQCODE123',
        ]);
        $this->assertDatabaseHas('anagrafiche_values', [
            'anagrafica_id' => $anagrafica->id,
            'attributes_id' => $attribute->id,
            'value' => 'new@example.com',
        ]);
    }
}
