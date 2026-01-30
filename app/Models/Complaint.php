<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'subject',
        'description',
        'attachment',
        'status',
        'admin_response',
        'handled_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Status Badge
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


    /**
     * Relationship to User (submitter)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to User (handler/admin)
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Polymorphic relationship to Status Histories
     */
    public function statusHistories()
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to ComplaintDocuments (multiple files)
     */
    public function documents()
    {
        return $this->hasMany(ComplaintDocument::class);
    }

    /**
     * Check if complaint has attachment
     */
    public function hasAttachment()
    {
        return !empty($this->attachment);
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }

    /**
     * Get attachment file extension
     */
    public function getAttachmentExtensionAttribute()
    {
        if ($this->attachment) {
            return pathinfo($this->attachment, PATHINFO_EXTENSION);
        }
        return null;
    }

    /**
     * Check if attachment is image
     */
    public function attachmentIsImage()
    {
        if (!$this->attachment) return false;
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array(strtolower($this->attachment_extension), $imageExtensions);
    }

    /**
     * Check if attachment is PDF
     */
    public function attachmentIsPdf()
    {
        if (!$this->attachment) return false;
        
        return strtolower($this->attachment_extension) === 'pdf';
    }

  
    public function getTicketIdAttribute()
    {
        return $this->ticket_number;
    }

    /**
     * Accessor: title -> subject
     */
    public function getTitleAttribute()
    {
        return $this->subject;
    }

    /**
     * Accessor: documents (untuk kompatibilitas PDF view)
     */
    public function getDocumentsAttribute()
    {
        if ($this->relationLoaded('documents') && $this->getRelation('documents')->count() > 0) {
            return $this->getRelation('documents');
        }
        
        if ($this->attributes['attachment'] ?? null) {
            return collect([
                (object)[
                    'file_path' => $this->attributes['attachment'],
                    'original_name' => basename($this->attributes['attachment']),
                    'file_size' => file_exists(storage_path('app/public/' . $this->attributes['attachment'])) 
                        ? filesize(storage_path('app/public/' . $this->attributes['attachment'])) 
                        : 0,
                ]
            ]);
        }
        
        return collect([]);
    }

    /**
     * Accessor: admin_notes -> admin_response 
     */
    public function getAdminNotesAttribute()
    {
        return $this->attributes['admin_response'] ?? null;
    }
}