<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
    ];

    /**
     * Get the parent trackable model (Submission, Consultation, or Complaint)
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the change
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}