<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalChain extends Model
{
    protected $table = 'approval_chains';

    protected $fillable = ['nama', 'slug'];

    public function steps()
    {
        return $this->hasMany(ApprovalChainStep::class);
    }
}
