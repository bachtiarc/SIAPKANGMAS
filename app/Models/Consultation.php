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

    public function getStatusBadgeAttribute()
    {
        $status = strtolower($this->status);

        // SELESAI = HIJAU
        if (in_array($status, ['completed', 'selesai'])) {
            return 'bg-green-100 text-green-800';
        }

        // DIPROSES = BIRU
        if (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
            return 'bg-blue-100 text-blue-800';
        }

        // MENUNGGU PROSES = KUNING
        if (in_array($status, ['pending', 'belum diproses'])) {
            return 'bg-yellow-100 text-yellow-800';
        }

        // DITOLAK = MERAH
        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'bg-red-100 text-red-800';
        }

        return 'bg-gray-100 text-gray-800';
    }

    // Status Label  
    public function getStatusLabelAttribute()
    {
        $status = strtolower($this->status);

        if (in_array($status, ['completed', 'selesai'])) {
            return 'Selesai';
        }

        if (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
            return 'Diproses';
        }

        if (in_array($status, ['pending', 'belum diproses'])) {
            return 'Menunggu Proses';
        }

        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'Ditolak';
        }

        return ucfirst($status);
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