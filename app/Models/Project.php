<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Bootstrap any application services.
     */
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
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function createTask(array $data)
    {
        return $this->tasks()->create($data);
    }

    public function assignUser(User $user, Role $role)
    {
        $this->users()->attach($user, ['role_id' => $role->id]);
    }

    public function detachUser(User $user)
    {
        $this->users()->detach($user);
    }

    public function getUserRole(User $user)
    {
        return $this->users()
            ->where('users.id', $user->id)
            ->first()
            ->pivot
            ->role_id;
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
