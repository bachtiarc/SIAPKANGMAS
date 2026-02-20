<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function provinces()
    {
        return DB::table('reg_provinces')
            ->select('code as kode', 'name as nama')
            ->orderBy('name')
            ->get();
    }

    public function regencies(string $provinsi)
    {
        return DB::table('reg_regencies')
            ->select('code as kode', 'name as nama')
            ->where('province_code', $provinsi)
            ->orderBy('name')
            ->get();
    }

    public function districts(string $kabupaten)
    {
        return DB::table('reg_districts')
            ->select('code as kode', 'name as nama')
            ->where('regency_code', $kabupaten)
            ->orderBy('name')
            ->get();
    }

    public function villages(string $kecamatan)
    {
        return DB::table('reg_villages')
            ->select('code as kode', 'name as nama')
            ->where('district_code', $kecamatan)
            ->orderBy('name')
            ->get();
    }
}