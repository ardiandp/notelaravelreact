<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $fillable = ['user_id', 'leave_type_id', 'year', 'kuota', 'terpakai'];

    protected function casts(): array
    {
        return ['year' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
