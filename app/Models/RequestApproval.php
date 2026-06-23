<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestApproval extends Model
{
    protected $table = 'request_approvals';

    protected $fillable = [
        'requestable_type', 'requestable_id',
        'approval_chain_step_id', 'step_order',
        'approver_id', 'status', 'catatan', 'action_at',
    ];

    protected function casts(): array
    {
        return ['action_at' => 'datetime'];
    }

    public function requestable()
    {
        return $this->morphTo();
    }

    public function approvalChainStep()
    {
        return $this->belongsTo(ApprovalChainStep::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
