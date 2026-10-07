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
            // Exactly one target: a subcategory, a category, or "out of its subcategory" (dropped on All)
            'subcategory_id' => ['required_without_all:category,remove_subcategory', 'prohibits:category,remove_subcategory', 'integer', 'exists:subcategories,id'],
            'category' => ['required_without_all:subcategory_id,remove_subcategory', 'prohibits:remove_subcategory', Rule::enum(PhotoCategory::class)],
            // Only checked when sent; a missing target is already reported by the two rules above
            'remove_subcategory' => ['sometimes', 'accepted'],
        ]);

        $photos = Photo::whereIn('id', $validated['photos']);
        $count = count($validated['photos']);
        $amount = "{$count} ".Str::plural('photo', $count);

        if (isset($validated['subcategory_id'])) {
            $photos->update(['subcategory_id' => $validated['subcategory_id']]);
            $message = "{$amount} moved to ".Subcategory::find($validated['subcategory_id'])->name.'.';
        } elseif (isset($validated['category'])) {
            $photos->update(['category' => $validated['category']]);
            $message = "{$amount} moved to ".PhotoCategory::from($validated['category'])->label().'.';
        } else {
            // The photos stay in the gallery, they just leave their subcategory
            $photos->update(['subcategory_id' => null]);
            $message = "{$amount} removed from their subcategory.";
        }

        return response()->json([
            'message' => $message,
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
