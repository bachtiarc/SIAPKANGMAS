<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nip',
        'email',
        'phone',
        'role',
        'bidang',
        'jabatan',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get all submissions for this user
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get all consultations for this user
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get all complaints for this user
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Get submissions handled by this admin
     */
    public function handledSubmissions()
    {
        return $this->hasMany(Submission::class, 'handled_by');
    }

    /**
     * Get consultations handled by this admin
     */
    public function handledConsultations()
    {
        return $this->hasMany(Consultation::class, 'handled_by');
    }

    /**
     * Get complaints handled by this admin
     */
    public function handledComplaints()
    {
        return $this->hasMany(Complaint::class, 'handled_by');
    }
}