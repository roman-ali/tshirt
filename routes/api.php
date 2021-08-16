<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function () {
    Route::post('/companies',             [CompanyController::class, 'store']);
    Route::get('/companies',              [CompanyController::class, 'index']);
    Route::get('/companies/{company}',    [CompanyController::class, 'show']);
    Route::put('/companies/{company}',    [CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
    
    Route::post('/contacts',             [ContactController::class, 'store']);
    Route::get('/contacts',              [ContactController::class, 'index']);
    Route::get('/contacts/{contact}',    [ContactController::class, 'show']);
    Route::put('/contacts/{contact}',    [ContactController::class, 'update']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
    
    Route::post('/notes',          [NoteController::class, 'store']);
    Route::get('/notes',           [NoteController::class, 'index']);
    Route::get('/notes/{note}',    [NoteController::class, 'show']);
    Route::put('/notes/{note}',    [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
});
