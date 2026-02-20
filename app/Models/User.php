<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type',
        'nip',
        'nik',
        'phone',
        'address',
        'pekerjaan',
        'bidang',
        'jabatan',
        'foto_ktp',
        'profile_photo',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
    ];

   
    protected $hidden = [
        'password',
        'remember_token',
    ];

  
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

   
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    
    public function handledSubmissions()
    {
        return $this->hasMany(Submission::class, 'handled_by');
    }

    
    public function handledConsultations()
    {
        return $this->hasMany(Consultation::class, 'handled_by');
    }

    
    public function handledComplaints()
    {
        return $this->hasMany(Complaint::class, 'handled_by');
    }

    
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        return Storage::disk('supabase_profile')->url($this->profile_photo);
    }

}