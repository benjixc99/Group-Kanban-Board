<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\HasApiTokens;
use Intervention\Image\Facades\Image;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function findByUid($uid): object
    {
        return self::where('uid', $uid)->first();
    }

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

    /*
     *  Display User Name
     */
    public function displayName(): string
    {
        return $this->name;
    }

    /**
     * Check if user has customer account.
     */
    public function isCustomer(): bool
    {
        return $this->role->name === 'customer';
    }

    /**
     * Check if user has developer account.
     */
    public function isDeveloper(): bool
    {
        return $this->role->name === 'developer';
    }

    /**
     * Check if user has project manager account.
     */
    public function isProjectManager(): bool
    {
        return $this->role->name === 'project_manager';
    }

    /**
     * Check if user has administrator account.
     */
    public function isAdministrator(): bool
    {
        return $this->role->name === 'administrator';
    }


    /**
     * Upload and resize avatar.
     *
     * @return string
     * @var void
     */
    public function uploadImage($file): string
    {
        $path        = 'app/profile/';
        $upload_path = storage_path($path);

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $filename = 'avatar-' . $this->id . '.' . $file->getClientOriginalExtension();

        // save to server
        $file->move($upload_path, $filename);

        // create thumbnails
        $img = Image::make($upload_path . $filename);

        $img->fit(120, 120, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })->save($upload_path . $filename . '.thumb.jpg');

        return $path . $filename;
    }


    /**
     * Get image thumb path.
     *
     * @return string
     * @var string
     */
    public function imagePath(): string
    {
        if (!empty($this->image) && !empty($this->id)) {
            return storage_path($this->image) . '.thumb.jpg';
        } else {
            return '';
        }
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function removeImage()
    {
        if (!empty($this->image) && !empty($this->id)) {
            $path = storage_path($this->image);
            if (is_file($path)) {
                unlink($path);
            }
            if (is_file($path . '.thumb.jpg')) {
                unlink($path . '.thumb.jpg');
            }
        }
    }


    public function getCanEditAttribute(): bool
    {
        return 1 === auth()->id();
    }

    public function getCanDeleteAttribute(): bool
    {
        return $this->id !== auth()->id() && (Gate::check('delete customer'));
    }


    public function getIsSuperAdminAttribute(): bool
    {
        return 1 === $this->id;
    }

    /**
     * One to One relation with RoleUser.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasRole($name): bool
    {
        return $this->role->name === $name;
    }


    /**
     * @return Collection
     */

    public function getPermissions(): Collection
    {
        return $this->role->permissions->pluck('name');
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

    /**
     * Get all users minus administrator.
     */
    public function scopeNonAdmin($query)
    {
        $admin = User::first();
        return $query->where('id', '!=', $admin->id)->with('role');
    }

    /**
     * Get all user projects through project_user table.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
