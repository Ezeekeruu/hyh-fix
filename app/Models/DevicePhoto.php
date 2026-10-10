<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DevicePhoto extends Model
{
     protected $fillable = [
        'repair_ticket_id',
        'photo_path',
        'photo_type',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function repairTicket()
    {
        return $this->belongsTo(RepairTicket::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Returns a ready-to-use photo URL, or null if the path is missing.
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? Storage::disk('s3')->url($this->photo_path) : null;
    }
}
