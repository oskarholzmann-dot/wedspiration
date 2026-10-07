<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PhotoCategory;
use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubcategoryController extends Controller
{
    /**
     * Create a new subcategory (from the form in the gallery).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:subcategories,name'],
        ], [], ['name' => 'subcategory name']);

        $subcategory = Subcategory::create($validated);

        return back()->with('success', "Subcategory \"{$subcategory->name}\" created. Drag photos onto it to sort them in.");
    }

    /**
     * Delete a subcategory. Its photos stay in the gallery, just without a subcategory.
     */
    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $subcategory->delete();

        return redirect()
            ->route('gallery.index')
            ->with('success', "Subcategory \"{$subcategory->name}\" deleted. Its photos are still in the gallery.");
    }

    /**
     * Move a group of photos at once: into a subcategory, or into one of the categories.
     * Used when several selected photos are dragged together in the gallery.
     */
    public function sortMany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['integer', 'exists:photos,id'],
            // Exactly one target: a subcategory or a category
            'subcategory_id' => ['required_without:category', 'prohibits:category', 'integer', 'exists:subcategories,id'],
            'category' => ['required_without:subcategory_id', Rule::enum(PhotoCategory::class)],
        ]);

        $photos = Photo::whereIn('id', $validated['photos']);

        if (isset($validated['subcategory_id'])) {
            $photos->update(['subcategory_id' => $validated['subcategory_id']]);
            $target = Subcategory::find($validated['subcategory_id'])->name;
        } else {
            $photos->update(['category' => $validated['category']]);
            $target = PhotoCategory::from($validated['category'])->label();
        }

        $count = count($validated['photos']);

        return response()->json([
            'message' => "{$count} ".Str::plural('photo', $count)." moved to {$target}.",
            'counts' => Subcategory::withCount('photos')->pluck('photos_count', 'id'),
        ]);
    }

    /**
     * Move a photo into a subcategory (drag and drop in the gallery), or out of it with an empty value.
     */
    public function assign(Request $request, Photo $photo): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,id'],
        ]);

        $photo->update(['subcategory_id' => $validated['subcategory_id'] ?? null]);

        $name = $photo->subcategory?->name;
        $message = $name ? "Moved to {$name}." : 'Removed from its subcategory.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'counts' => Subcategory::withCount('photos')->pluck('photos_count', 'id'),
            ]);
        }

        return back()->with('success', $message);
    }
}
