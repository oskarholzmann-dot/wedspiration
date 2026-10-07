<?php

namespace App\Models;

use App\Enums\PhotoCategory;
use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
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
     * Get the public URL of the image: an external link, a seeded sample image or an uploaded file.
     *
     * @return Attribute<string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            Str::startsWith($this->image_path, ['http://', 'https://']) => $this->image_path,
            Str::startsWith($this->image_path, 'images/seed/') => asset($this->image_path),
            default => Storage::disk('public')->url($this->image_path),
        });
    }

    /**
     * Whether the image was uploaded by a user (and lives on the public storage disk).
     */
    public function isUploaded(): bool
    {
        return ! Str::startsWith($this->image_path, ['http://', 'https://', 'images/seed/']);
    }

    /**
     * Delete the uploaded image file. External links and seeded sample images are left alone.
     */
    public function deleteImageFile(): void
    {
        if ($this->isUploaded()) {
            Storage::disk('public')->delete($this->image_path);
        }
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
