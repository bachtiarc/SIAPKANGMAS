<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'submitted_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel dokumen baru (Multiple Files)
     */
    public function documents()
    {
        return $this->hasMany(ConsultationDocument::class, 'consultation_id');
    }

    /**
     * PERBAIKAN: Menggunakan morphMany karena tabel status_histories 
     * menggunakan trackable_type dan trackable_id
     */
    public function statusHistories()
    {
        return $this->morphMany(ConsultationStatusHistory::class, 'trackable')->latest();
    }

    // --- Accessor Status Badge ---
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'diproses' => 'bg-blue-100 text-blue-800',
            'selesai' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // --- Accessor Status Label ---
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    // --- Relationships ---
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
    
    public function getTicketIdAttribute()
    {
        return $this->ticket_number;
    }

    public function getTitleAttribute()
    {
        return $this->subject;
    }

    public function getAdminNotesAttribute()
    {
        return $this->attributes['admin_response'] ?? $this->attributes['admin_notes'] ?? null;
    }

    // --- Scopes ---
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUserType($query, $type)
    {
        return $query->whereHas('user', function($q) use ($type) {
            $q->where('user_type', $type);
        });
    }
}