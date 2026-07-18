<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    protected $fillable = ['user_id', 'module_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}