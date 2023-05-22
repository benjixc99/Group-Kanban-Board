<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['name', 'description', 'priority', 'status', 'estimate', 'assignee_id', 'priority_id', 'status_id'];

    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (self::where('uid', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function getCommentsAttribute()
    {
        return $this->comments()->orderBy('created_at', 'desc')->get();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function updateProgress($progress)
    {
        $this->progress = $progress;
        $this->save();
    }

    public function addComment($comment)
    {
        $this->comments()->create(['comment' => $comment]);
    }

    public function editComment($commentId, $newComment)
    {
        $comment = $this->comments()->find($commentId);
        if ($comment) {
            $comment->comment = $newComment;
            $comment->save();
        }
    }

    public function deleteComment($commentId)
    {
        $comment = $this->comments()->find($commentId);
        if ($comment) {
            $comment->delete();
        }
    }

    /**
     * get route key by uid
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }
}
