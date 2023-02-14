<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Task $task)
    {
        if ($user->id == $task->user_id) return true;
        return false;
    }

    public function destroy(User $user, Task $task)
    {
        if ($user->id == $task->user_id) return true;
        return false;
    }

    public function share(User $user, Task $task)
    {
        if ($user->id == $task->user_id) return true;
        return false;
    }
}
