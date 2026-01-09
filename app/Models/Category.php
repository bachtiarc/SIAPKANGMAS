<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get submissions with this category
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get consultations with this category
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get complaints with this category
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}