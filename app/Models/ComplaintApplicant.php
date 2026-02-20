<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintApplicant extends Model
{
    protected $fillable = [
        'complaint_id',
        'nama_lengkap',
        'nik',
        'email',
        'pekerjaan',
        'phone',
        'alamat_detail',
        'kabupaten_kode',
        'kabupaten_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'desa_kode',
        'desa_nama',
        'provinsi',
        'foto_ktp',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}