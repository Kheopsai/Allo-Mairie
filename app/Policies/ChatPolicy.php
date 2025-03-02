<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\SyncedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|SyncedUser $user):bool
    {
        return $user->hasPermission('chat-index');
    }

    public function view(User|SyncedUser $user,Chat $chat):bool
    {
        return $user->hasPermission('chat-read');
    }

    public function create(User|SyncedUser $user):bool
    {
        return $user->hasPermission('chat-create');
    }


    public function update(User|SyncedUser $user,Chat $chat):bool
    {
        return $user->hasPermission('chat-update');
    }

    public function delete(User|SyncedUser $user,Chat $chat):bool
    {
        return $user->hasPermission('chat-delete');
    }
}
