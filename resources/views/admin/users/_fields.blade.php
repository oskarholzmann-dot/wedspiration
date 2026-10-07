{{-- Shared form fields for creating and editing a user. Optionally expects $user. --}}
<div>
    <x-breeze.input-label for="name" value="Name" />
    <x-breeze.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-breeze.input-label for="email" value="Email" />
    <x-breeze.text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('email')" />
</div>

<div>
    <x-breeze.input-label for="password" :value="isset($user) ? 'New password (leave empty to keep the current one)' : 'Password'" />
    <x-breeze.text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" :required="! isset($user)" />
    <x-breeze.input-error class="mt-2" :messages="$errors->get('password')" />
</div>

<div>
    {{-- Hidden field: an unchecked checkbox sends nothing, so this sends 0 instead --}}
    <input type="hidden" name="is_admin" value="0">
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_admin" value="1"
               class="rounded border-gray-400"
               @checked(old('is_admin', $user->is_admin ?? false))
               @disabled(isset($user) && $user->is(Auth::user()))>
        <span class="text-sm text-gray-700">Administrator</span>
    </label>
    @if (isset($user) && $user->is(Auth::user()))
        <p class="mt-1 text-xs text-gray-500">You cannot remove your own admin rights.</p>
    @endif
    <x-breeze.input-error class="mt-2" :messages="$errors->get('is_admin')" />
</div>
