<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'status_histories'; 

    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
    ];

 
    public function trackable()
    {
        return $this->morphTo();
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}