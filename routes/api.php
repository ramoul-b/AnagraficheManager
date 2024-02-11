<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnagraficheController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/anagrafiche', [AnagraficheController::class, 'index']);
Route::get('/anagrafiche/{id}', [AnagraficheController::class, 'show']);
Route::post('/anagrafiche', [AnagraficheController::class, 'store']);
Route::put('/anagrafiche/{id}', [AnagraficheController::class, 'update']);
Route::delete('/anagrafiche/{id}', [AnagraficheController::class, 'destroy']);
