<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id', 'leave_type_id', 'approval_chain_id',
        'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari',
        'keterangan', 'attachment', 'status', 'tanggal_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvalChain()
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    public function approvals()
    {
        return $this->morphMany(RequestApproval::class, 'requestable');
    }
}
