<?php

namespace App\Http\Controllers;

use App\Models\Anagrafiche;
use Illuminate\Http\Request;
use Validator;
use App\Rules\PartitaIvaRule; 
use App\Rules\CodiceFiscaleRule; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\AnagraficheValue;

/**
 * @OA\Info(title="API Anagrafiche", version="1.0.0", description="API pour la gestion des anagrafiches")
 */

class AnagraficheController extends Controller
{


    /**
 * @OA\Get(
 *     path="/api/anagrafiche",
 *     summary="List anagrafiche",
 *     tags={"Anagrafiche"},
 *     @OA\Response(
 *         response=200,
 *         description="A list with anagrafiche"
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="perPage",
 *         in="query",
 *         description="Items per page",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     )
 * )
 */
public function index()
{
    $anagrafiches = Anagrafiche::with('tipi')->paginate(10)->through(function ($anagrafiche) {
        return [
            'partita_iva' => $anagrafiche->partita_iva,
            'codice_fiscale' => $anagrafiche->codice_fiscale,
            'unique_code' => $anagrafiche->unique_code,
            'tipo_nome' => $anagrafiche->tipi ? $anagrafiche->tipi->nome : null,
        ];
    });

    return response()->json([
        'success' => true,
        'data' => [
            'items' => $anagrafiches->items(),
            'pagination' => [
                'total' => $anagrafiches->total(),
                'perPage' => $anagrafiches->perPage(),
                'currentPage' => $anagrafiches->currentPage(),
                'lastPage' => $anagrafiches->lastPage(),
            ],
        ]
    ]);
}

    


/**
 * @OA\Get(
 *     path="/api/anagrafiche/{id}",
 *     operationId="getAnagraficaById",
 *     tags={"Anagrafiche"},
 *     summary="Get an anagrafica by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the anagrafica",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="items", type="object",
 *                     @OA\Property(property="partita_iva", type="string", example="12345678901"),
 *                     @OA\Property(property="codice_fiscale", type="string", example="CODFSC12A34B567C"),
 *                     @OA\Property(property="unique_code", type="string", example="UNQCOD123456"),
 *                     @OA\Property(property="tipo_id", type="integer", example=1)
 *                 ),
 *                 @OA\Property(property="pagination", type="object", example=null),
 *             ),
 *             @OA\Property(property="error", type="string", example=null),
 *             @OA\Property(property="errors", type="object", example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Anagrafica not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Anagrafica not found"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="errorDetail", type="string", example="No anagrafica found with the specified ID")
 *             )
 *         )
 *     ),
 * )
 */
public function show($id)
{
    $anagrafica = Anagrafiche::with(['tipi', 'values.attribute'])->find($id);

    if (!$anagrafica) {
        return response()->json([
            'success' => false,
            'error' => 'Not found resource',
            'errors' => [
                'error' => 'Anagrafica not found',
                'errorDetail' => 'No anagrafica found with the specified ID'
            ],
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'items' => $anagrafica,
            'pagination' => null, 
        ],
    ], 200);
}




/**
 * @OA\Post(
 *     path="/api/anagrafiche",
 *     tags={"Anagrafiche"},
 *     summary="Create a new anagrafica",
 *     operationId="storeAnagrafica",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"partita_iva", "codice_fiscale", "tipo_id", "unique_code"},
 *             @OA\Property(property="partita_iva", type="string", example="01234567890"),
 *             @OA\Property(property="codice_fiscale", type="string", example="RSSMRA85M01H501Z"),
 *             @OA\Property(property="tipo_id", type="integer", example=1),
 *             @OA\Property(property="unique_code", type="string", example="UNIQCODE123456"),
 *             @OA\Property(
 *                 property="attributes",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="attribute_id", type="integer", example=1),
 *                     @OA\Property(property="value", type="string", example="Attribute Value")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="items", type="object",
 *                     @OA\Property(property="partita_iva", type="string", example="01234567890"),
 *                     @OA\Property(property="codice_fiscale", type="string", example="RSSMRA85M01H501Z"),
 *                     @OA\Property(property="unique_code", type="string", example="UNIQCODE123456"),
 *                     @OA\Property(property="tipo_id", type="integer", example=1),
 *                     @OA\Property(property="attributes", type="array", 
 *                         @OA\Items(
 *                             type="object",
 *                             @OA\Property(property="attribute_id", type="integer", example=1),
 *                             @OA\Property(property="value", type="string", example="Attribute Value")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Validation errors"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"partita_iva": "La partita IVA non è valida."}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *     ),
 * )
 */




public function store(Request $request)
{
    Log::info('Store method called', ['request' => $request->all()]);
    // Récupérer les informations du tipo sélectionné
     $tipo = DB::table('tipi')->find($request->tipo_id);

    // Validation initiale
    $validationRules = [
        'unique_code' => 'required|string|max:255',
        'tipo_id' => 'required|integer|exists:tipi,id',
        'attributes' => 'required|array',
        'attributes.*.attribute_id' => 'required|integer|exists:attributes,id',
        'attributes.*.value' => 'required|string',
    ];

    // Ajout de règles conditionnelles pour partita_iva et codice_fiscale
    if ($tipo && $tipo->has_partita_iva) {
        $validationRules['partita_iva'] = $tipo->required_partita_iva ? ['required', 'string', 'max:11', new PartitaIvaRule()] : 'nullable|string|max:11';
    }
    if ($tipo && $tipo->has_codice_fiscale) {
        $validationRules['codice_fiscale'] = $tipo->required_codice_fiscale ? ['required', 'string', 'max:16', new CodiceFiscaleRule()] : 'nullable|string|max:16';
    }

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
        Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
        return response()->json([
            'success' => false,
            'error' => "Validation errors",
            'errors' => $validator->errors(),
        ], 400);
    }

    // Traitement de la création d'anagrafica et des attributs
    DB::beginTransaction();
    Log::info('Validation passed');
    try {
        $anagraficaData = $request->only(['partita_iva', 'codice_fiscale', 'unique_code', 'tipo_id']);
        $anagrafica = Anagrafiche::create($anagraficaData);
        Log::info('Anagrafica created', ['anagrafica' => $anagrafica]);
        Log::info('Checking attributes data', ['attributes' => $request->attributes]);
        $attributesData = $request->input('attributes');
        foreach ($attributesData as $attribute) {
            Log::info('Attempting direct insertion for attribute', ['attribute' => $attribute]);
            AnagraficheValue::create([
                'anagrafica_id' => $anagrafica->id,
                'attributes_id' => $attribute['attribute_id'],
                'value' => $attribute['value'],
            ]);
        }
        
        Log::info('All attributes inserted', ['anagrafica_id' => $anagrafica->id]);
        DB::commit();
        return response()->json(['success' => true, 'data' => $anagrafica], 200);
    } catch (\Exception $e) {
        Log::error('Error in store method', ['error' => $e->getMessage()]);
        DB::rollBack();
        return response()->json(['success' => false, 'error' => 'Server error', 'message' => $e->getMessage()], 500);
    }
}

 
/**
 * @OA\Put(
 *     path="/api/anagrafiche/{id}",
 *     tags={"Anagrafiche"},
 *     summary="Update an existing anagrafica",
 *     operationId="updateAnagrafica",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the anagrafica to update",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="partita_iva", type="string", example="01234567890"),
 *             @OA\Property(property="codice_fiscale", type="string", example="RSSMRA85M01H501Z"),
 *             @OA\Property(property="unique_code", type="string", example="UNIQCODE123456"),
 *             @OA\Property(property="tipo_id", type="integer", example=2),
 *             @OA\Property(
 *                 property="attributes",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="attribute_id", type="integer", example=1),
 *                     @OA\Property(property="value", type="string", example="Attribute Value")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="items", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Validation errors"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Anagrafica not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Anagrafica not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Internal server error")
 *         )
 *     )
 * )
 */

 public function update(Request $request, $id)
 {
     $anagrafica = Anagrafiche::find($id);
     $tipo = DB::table('tipi')->find($request->tipo_id);

     if (!$anagrafica) {
         return response()->json(['success' => false, 'error' => "Anagrafica not found"], 404);
     }
 
     $validationRules = [
         'unique_code' => 'sometimes|required|string|max:255',
         'tipo_id' => 'sometimes|required|integer|exists:tipi,id',
         'attributes' => 'sometimes|array',
         'attributes.*.attribute_id' => 'required|integer|exists:attributes,id',
         'attributes.*.value' => 'required|string',
     ];

     if ($tipo && $tipo->has_partita_iva) {
        $validationRules['partita_iva'] = $tipo->required_partita_iva ? ['sometimes', 'required', 'string', 'max:11', new PartitaIvaRule()] : ['sometimes', 'nullable', 'string', 'max:11'];
    }
    if ($tipo && $tipo->has_codice_fiscale) {
        $validationRules['codice_fiscale'] = $tipo->required_codice_fiscale ? ['sometimes', 'required', 'string', 'max:16', new CodiceFiscaleRule()] : ['sometimes', 'nullable', 'string', 'max:16'];
    }
     $validator = Validator::make($request->all(), $validationRules);
 
     if ($validator->fails()) {
         return response()->json(['success' => false, 'error' => "Validation errors", 'errors' => $validator->errors()], 400);
     }
 
     $anagrafica->update($request->only(['partita_iva', 'codice_fiscale', 'unique_code', 'tipo_id']));
 
     // Supprimer les valeurs d'attributs existantes avant de les réinsérer
     $anagrafica->values()->delete();
 
     foreach ($request->attributes as $attribute) {
         AnagraficheValue::create([
             'anagrafica_id' => $anagrafica->id,
             'attribute_id' => $attribute['attribute_id'],
             'value' => $attribute['value'],
         ]);
     }
 
     return response()->json(['success' => true, 'data' => $anagrafica->fresh()], 200);
 }
 
 
/**
 * @OA\Delete(
 *     path="/api/anagrafiche/{id}",
 *     tags={"Anagrafiche"},
 *     summary="Delete an anagrafica",
 *     description="Soft deletes an anagrafica by setting the deleted_at timestamp.",
 *     operationId="deleteAnagrafica",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the anagrafica to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Anagrafica deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Anagrafica deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Anagrafica not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Anagrafica not found")
 *         )
 *     )
 * )
 */

 public function destroy($id)
 {
     $anagrafica = Anagrafiche::find($id);
 
     if (!$anagrafica) {
         return response()->json([
             'success' => false,
             'error' => 'Anagrafica not found',
         ], 404);
     }
     
     // Supprimer les valeurs d'attributs associées
     $anagrafica->values()->delete();
 
     $anagrafica->delete();
 
     return response()->json([
         'success' => true,
         'message' => 'Anagrafica and related attributes values deleted successfully',
     ], 200);
 }
 
 


}
