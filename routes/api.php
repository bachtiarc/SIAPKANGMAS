<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Route ini dipakai buat dropdown alamat (Jawa Tengah)
*/

Route::get('/wilayah/kabupaten', function () {
    return DB::table('wilayah')
        ->select('kode', 'nama')
        ->where('level', 2)
        ->where('parent', '33') // Provinsi Jawa Tengah
        ->orderBy('nama')
        ->get();
});

Route::get('/wilayah/kecamatan/{kab}', function ($kab) {
    return DB::table('wilayah')
        ->select('kode', 'nama')
        ->where('level', 3)
        ->where('parent', $kab)
        ->orderBy('nama')
        ->get();
});

Route::get('/wilayah/desa/{kec}', function ($kec) {
    return DB::table('wilayah')
        ->select('kode', 'nama')
        ->where('level', 4)
        ->where('parent', $kec)
        ->orderBy('nama')
        ->get();
});
