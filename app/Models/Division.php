<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'divisions';

    protected $fillable = ['nama_bagian', 'parent_id', 'head_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'division_id');
    }
}
