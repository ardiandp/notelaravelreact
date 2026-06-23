<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $fillable = ['nama', 'slug', 'deskripsi', 'kuota_per_tahun', 'is_paid', 'requires_attachment', 'is_active', 'max_days', 'approval_chain_id'];

    protected function casts(): array
    {
        return ['is_paid' => 'boolean', 'requires_attachment' => 'boolean', 'is_active' => 'boolean'];
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function approvalChain()
    {
        return $this->belongsTo(ApprovalChain::class);
    }
}
