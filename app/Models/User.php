<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Register model events.
     */
    protected static function booted(): void
    {
        // The database deletes the user's photos (cascade), but not their image files
        static::deleting(function (User $user) {
            $user->photos->each->deleteImageFile();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the wedding folders owned by the user.
     *
     * @return HasMany<WeddingFolder, $this>
     */
    public function folders(): HasMany
    {
        return $this->hasMany(WeddingFolder::class);
    }

    /**
     * Get the user's main wedding folder, creating it when the user has none yet.
     */
    public function defaultFolder(): WeddingFolder
    {
        return $this->folders()->oldest('id')->first()
            ?? $this->folders()->create(['name' => $this->name."'s wedding"]);
    }

    // AI-GENERATED (beyond course scope): saving photos into your folder — written with Claude Code
    /**
     * Ids of the photos in the user's folder, loaded once per request (used to mark photos as saved).
     *
     * @return list<int>
     */
    public function savedPhotoIds(): array
    {
        return once(fn () => $this->folders()->oldest('id')->first()?->photos()->pluck('photos.id')->all() ?? []);
    }

    /**
     * Get the photos uploaded by the user.
     *
     * @return HasMany<Photo, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
