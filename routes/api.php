<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/properties',[\App\Http\Controllers\PropertiesController::class,'GetPropertiesApi']);
Route::get('/properties/{portal}/{id_key}',[\App\Http\Controllers\PropertiesController::class,'GetPropertiesApi']);

Route::post('/company/store',[\App\Http\Controllers\CompanyController::class,'apiStore']);
Route::post('/company/edit',[\App\Http\Controllers\CompanyController::class,'apiEdit']);
Route::post('/lead/website-store',[\App\Http\Controllers\LeadController::class,'apiStore']);
Route::post('/lead/store/{id_key}',[\App\Http\Controllers\LeadController::class,'insertDubizzleLeads']);//
Route::post('/off-plan/insert',[\App\Http\Controllers\OffPlanProjectController::class,'insertOffPlan']);//


