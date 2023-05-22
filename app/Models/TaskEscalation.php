<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskEscalation extends Model
{
    protected $fillable = [
        'task_id', 'user_id', 'reason',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
