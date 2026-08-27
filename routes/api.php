<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


use App\Http\Controllers\Api\LocationController;

Route::get('/location/provinces', [LocationController::class, 'getProvinces']);
Route::get('/location/wards/{provinceCode}', [LocationController::class, 'getWards']);

