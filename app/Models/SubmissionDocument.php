<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Get the submission that owns the document
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}