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
        'submitted_at' => 'datetime',
    ];

    // Status badge colors
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

    // Status label
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

    // Relationships
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

    // Jika punya table consultation_documents
    public function documents()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
    
    public function getTicketIdAttribute()
    {
        return $this->ticket_number;
    }

    public function getTitleAttribute()
    {
        return $this->subject;
    }

    public function getDocumentsAttribute()
    {
        if ($this->attachment) {
            return collect([
                (object)[
                    'file_path' => $this->attachment,
                    'original_name' => basename($this->attachment),
                    'file_size' => file_exists(storage_path('app/public/' . $this->attachment)) 
                        ? filesize(storage_path('app/public/' . $this->attachment)) 
                        : 0,
                ]
            ]);
        }
        return collect([]);
    }

    public function getAdminNotesAttribute()
    {
        return $this->attributes['admin_response'] ?? $this->attributes['admin_notes'] ?? null;
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