<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionApplicant extends Model
{
    protected $fillable = [
        'submission_id',
        'nama_lengkap',
        'nik',
        'email',
        'phone',
        'pekerjaan',
        'alamat_detail',
        'kabupaten_kode',
        'kabupaten_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'desa_kode',
        'desa_nama',
        'provinsi',
        'is_kelurahan',
        'foto_ktp',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}