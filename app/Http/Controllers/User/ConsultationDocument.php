<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}