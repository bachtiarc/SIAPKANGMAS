<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'nama_lengkap',
        'nik',
        'email',
        'phone',
        'pekerjaan',
        'alamat_detail',
        'provinsi_kode',
        'provinsi',
        'kabupaten_kode',
        'kabupaten_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'desa_kode',
        'desa_nama',
        'foto_ktp',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}