<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isSalesClerk(): bool
    {
        // Legacy 'staff' rows (created before the three-role migration)
        // keep clerk-level access.
        return $this->role === 'sales-clerk' || $this->role === 'staff';
    }

    public function isStaff(): bool
    {
        return $this->isTechnician() || $this->isSalesClerk();
    }

    public function canAccessPos(): bool
    {
        return $this->isAdmin() || $this->isSalesClerk();
    }

    public function canViewTransactions(): bool
    {
        return $this->isAdmin() || $this->isSalesClerk();
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'technician' => 'Technician',
            'sales-clerk' => 'Sales Clerk',
            default => 'Staff',
        };
    }

    public function assignedRepairTickets()
    {
        return $this->hasMany(RepairTicket::class, 'assigned_to');
    }

    public function repairStatusHistories()
    {
        return $this->hasMany(RepairStatusHistory::class, 'changed_by');
    }

    public function devicePhotos()
    {
        return $this->hasMany(DevicePhoto::class, 'uploaded_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}