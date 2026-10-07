<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeddingFolder;

class WeddingFolderPolicy
{
    /**
     * Determine whether the user can view the folder.
     */
    public function view(User $user, WeddingFolder $folder): bool
    {
        return $folder->user_id === $user->id;
    }

    /**
     * Determine whether the user can edit the folder.
     */
    public function update(User $user, WeddingFolder $folder): bool
    {
        return $folder->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the folder.
     */
    public function delete(User $user, WeddingFolder $folder): bool
    {
        return $folder->user_id === $user->id;
    }
}
