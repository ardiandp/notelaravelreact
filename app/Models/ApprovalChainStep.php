<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalChainStep extends Model
{
    protected $table = 'approval_chain_steps';

    protected $fillable = ['approval_chain_id', 'step_order', 'approver_type', 'role_id'];

    public function chain()
    {
        return $this->belongsTo(ApprovalChain::class, 'approval_chain_id');
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }
}
