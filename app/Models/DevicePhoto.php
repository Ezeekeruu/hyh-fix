<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    // Built from config only (never boots the S3 client) so pages render
    // even when AWS credentials are absent; falls back to local storage.
    public function getPhotoUrlAttribute()
    {
        if (empty($this->photo_path)) {
            return null;
        }

        $base = rtrim((string) config('filesystems.disks.s3.url'), '/');

        return $base !== '' ? $base.'/'.$this->photo_path : asset('storage/'.$this->photo_path);
    }
}
