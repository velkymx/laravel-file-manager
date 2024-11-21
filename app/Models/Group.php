<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'can_upload', 'can_download', 'can_delete'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
