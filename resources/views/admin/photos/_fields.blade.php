{{-- Shared form fields for creating and editing a photo. Expects $categories and optionally $photo. --}}
<div>
    <x-breeze.input-label for="title" value="Title" />
    <x-breeze.text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $photo->title ?? '')" required />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div>
    <x-breeze.input-label for="description" value="Description (optional)" />
    <textarea id="description" name="description" rows="3"
              class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">{{ old('description', $photo->description ?? '') }}</textarea>
    <x-breeze.input-error class="mt-2" :messages="$errors->get('description')" />
</div>

<div>
    <x-breeze.input-label for="category" value="Category (optional)" />
    <select id="category" name="category"
            class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">
        <option value="">- No category -</option>
        @foreach ($categories as $category)
            <option value="{{ $category->value }}" @selected(old('category', isset($photo) ? $photo->category?->value : null) === $category->value)>
                {{ $category->label() }}
            </option>
        @endforeach
    </select>
    <x-breeze.input-error class="mt-2" :messages="$errors->get('category')" />
</div>

<div>
    <x-breeze.input-label for="image" :value="isset($photo) ? 'Replace image (optional, max. 2 MB)' : 'Image (max. 2 MB)'" />
    <input id="image" name="image" type="file" accept="image/*" @required(! isset($photo))
           class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-white hover:file:bg-gray-700">
    <x-breeze.input-error class="mt-2" :messages="$errors->get('image')" />
</div>
