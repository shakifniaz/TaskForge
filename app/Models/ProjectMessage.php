<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
