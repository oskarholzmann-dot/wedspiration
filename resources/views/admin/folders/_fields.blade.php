{{-- Shared form fields for creating and editing a folder. Optionally expects $folder. --}}
<div>
    <x-breeze.input-label for="name" value="Name" />
    <x-breeze.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $folder->name ?? '')" required />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-breeze.input-label for="wedding_date" value="Wedding date (optional)" />
    <x-breeze.text-input id="wedding_date" name="wedding_date" type="date" class="mt-1 block w-full"
                         :value="old('wedding_date', isset($folder) ? $folder->wedding_date?->format('Y-m-d') : '')" />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('wedding_date')" />
</div>

<div>
    <x-breeze.input-label for="notes" value="Notes for the photographer (optional)" />
    <textarea id="notes" name="notes" rows="4"
              class="mt-1 block w-full border border-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs px-2 py-1">{{ old('notes', $folder->notes ?? '') }}</textarea>
    <x-breeze.input-error class="mt-2" :messages="$errors->get('notes')" />
</div>
