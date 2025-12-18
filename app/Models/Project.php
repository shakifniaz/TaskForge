<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'github_repo',
        'github_default_branch',
        'github_token',
    ];

    protected $casts = [
        'github_token' => 'encrypted',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function files()
    {
        return $this->hasMany(\App\Models\ProjectFile::class);
    }

}
