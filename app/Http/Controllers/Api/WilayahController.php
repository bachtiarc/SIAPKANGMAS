<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function kabupaten()
    {
        return DB::table('wilayah')
            ->where('parent', '33')
            ->where('level', 2)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
    }

    public function kecamatan($kodeKab)
    {
        return DB::table('wilayah')
            ->where('parent', $kodeKab)
            ->where('level', 3)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
    }

    public function desa($kodeKec)
    {
        return DB::table('wilayah')
            ->where('parent', $kodeKec)
            ->where('level', 4)
            ->orderBy('nama')
            ->get(['kode', 'nama']);
    }
}