<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WilayahController;

Route::get('/provinsi', [WilayahController::class, 'provinces']);
Route::get('/kabupaten/{provinsi}', [WilayahController::class, 'regencies']);
Route::get('/kecamatan/{kabupaten}', [WilayahController::class, 'districts']);
Route::get('/desa/{kecamatan}', [WilayahController::class, 'villages']);