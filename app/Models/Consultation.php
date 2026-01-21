<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatusHistory;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'consultation_type',
        'subject',
        'description',
        'attachment',
        'status',
        'admin_response',
        'admin_notes',
        'handled_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the consultation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the consultation.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the handler (admin) of the consultation.
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get all status histories for the consultation.
     */
    public function statusHistories()
    {
        return $this->morphMany(ConsultationStatusHistory::class)->latest();
    }

    /**
     * Get the documents for the consultation.
     */
    public function documents()
    {
        return $this->hasMany(ConsultationDocument::class);
    }

    /**
     * Scope to filter consultations by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter consultations by user type.
     */
    public function scopeUserType($query, $type)
    {
        return $query->whereHas('user', function($q) use ($type) {
            $q->where('user_type', $type);
        });
    }
}