<?php

namespace App\Models;

use App\Enums\PhotoCategory;
use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
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
        'subcategory_id',
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
     * Newest first. Photos uploaded in the same second are ordered by id, so the order never jumps around.
     *
     * @param  Builder<Photo>  $query
     */
    public function scopeNewestFirst(Builder $query): void
    {
        $query->orderByDesc($query->qualifyColumn('created_at'))
            ->orderByDesc($query->qualifyColumn('id'));
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
            // AI-GENERATED (beyond course scope): upload URLs independent of APP_URL — written with Claude Code
            // asset() uses the address the page is opened with, so uploads also show when APP_URL
            // in .env does not match (e.g. "php artisan serve" on port 8000 with APP_URL=http://localhost)
            default => asset('storage/'.$this->image_path),
        });
    }

    // AI-GENERATED (beyond course scope): image sizes for the stable masonry layout — written with Claude Code
    /**
     * Width and height of the image in pixels, read once from the file and then remembered.
     * Lets the gallery reserve the right space before the image has loaded (no jumping layout).
     *
     * @return Attribute<array{width: int, height: int}, never>
     */
    protected function imageSize(): Attribute
    {
        return Attribute::get(fn () => Cache::rememberForever('photo-size:'.$this->image_path, function () {
            $file = match (true) {
                Str::startsWith($this->image_path, ['http://', 'https://']) => null,
                Str::startsWith($this->image_path, 'images/seed/') => public_path($this->image_path),
                default => Storage::disk('public')->path($this->image_path),
            };

            $size = $file && is_file($file) ? getimagesize($file) : false;

            // Unknown (external link or missing file): assume a typical 4:3 photo
            return $size ? ['width' => $size[0], 'height' => $size[1]] : ['width' => 1200, 'height' => 900];
        }));
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

    // AI-GENERATED (beyond course scope): subcategories — written with Claude Code
    /**
     * Get the subcategory the photo is in (admins sort photos into these).
     *
     * @return BelongsTo<Subcategory, $this>
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
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
