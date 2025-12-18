<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'description',
        'start_date',
        'target_date',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ tasks under this milestone
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
