<?php

namespace App\Models;

use App\Enums\PhotoCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'image_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => PhotoCategory::class,
        ];
    }

    /**
     * Get the user who uploaded the photo.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the folders the photo is in.
     *
     * @return BelongsToMany<WeddingFolder, $this>
     */
    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(WeddingFolder::class, 'folder_photo', 'photo_id', 'folder_id');
    }
}
