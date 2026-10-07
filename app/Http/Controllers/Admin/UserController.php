<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a list of all users.
     */
    public function index(): View
    {
        $users = User::withCount(['folders', 'photos'])->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a new user and give them a wedding folder.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->only(['name', 'email', 'password']));
        // is_admin is not mass assignable on purpose, so it is set explicitly
        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        $user->defaultFolder();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'The user was created.');
    }

    /**
     * Display one user with their folders and photos.
     */
    public function show(User $user): View
    {
        $user->load('folders')->loadCount('photos');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the user. An empty password field keeps the current password.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->password = $request->validated('password');
        }

        // Admins cannot take away their own admin rights, so they can't lock themselves out
        if ($user->isNot($request->user())) {
            $user->is_admin = $request->boolean('is_admin');
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'The user was updated.');
    }

    /**
     * Delete the user together with their folders, photos and image files.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account here.']);
        }

        // Folders and photos are removed by the database (cascade), the files are not
        $user->photos->each->deleteImageFile();
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'The user was deleted.');
    }
}
