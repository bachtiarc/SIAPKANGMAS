<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'changed_by',
        'old_status',
        'new_status',
        'notes',
    ];

    /**
     * Get the consultation that owns the status history.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}