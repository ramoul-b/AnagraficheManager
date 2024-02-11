<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions; // Use DatabaseTransactions instead of RefreshDatabase
use Tests\TestCase;
use App\Models\Tipi;
use App\Models\Attribute;

class AnagraficheControllerTest extends TestCase
{
    use DatabaseTransactions; 
    /** @test */
    public function it_can_create_an_anagrafica()
    {
        // Création d'un enregistrement tipo pour passer la validation
        $tipo = Tipi::create([
            'nome' => 'Impresa',
            'descrizione' => 'Descrizione del tipo Impresa',
            'has_partita_iva' => 1,
            'required_partita_iva' => 1,
            'has_codice_fiscale' => 1,
            'required_codice_fiscale' => 1
        ]);

        // Création d'un attribut pour l'utiliser dans le test
        $attribute = Attribute::create(['name' => 'Email']);

        // Arrange : Préparer les données nécessaires
        $anagraficaData = [
            'partita_iva' => '01794980191',
            'codice_fiscale' => '01794980191',
            'unique_code' => 'UNIQ1234567890',
            'tipo_id' => $tipo->id, // Utilisation de l'ID du tipo créé
            'attributes' => [
                ['attribute_id' => 1, 'value' => 'example@example.com'] // Utilisation de l'ID de l'attribut créé
            ]
        ];

        // Act : Exécuter la requête à tester
        $response = $this->postJson('/api/anagrafiche', $anagraficaData);

        // Assert : Vérifier que tout s'est passé comme prévu
        $response->assertStatus(200);
        $this->assertDatabaseHas('anagrafiche', [
            'partita_iva' => '01794980191',
            'codice_fiscale' => '01794980191',
            'unique_code' => 'UNIQ1234567890'
        ]);
        $this->assertDatabaseHas('anagrafiche_values', [
            'value' => 'example@example.com'
        ]);
    }
}
