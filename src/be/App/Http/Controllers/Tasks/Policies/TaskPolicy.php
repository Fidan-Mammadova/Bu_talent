<?php

namespace App\Http\Controllers\Tasks\Policies;

use App\Http\Controllers\Tasks\Models\Customer;
use App\Http\Controllers\User\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function createAny(User $user): bool
    {
        return true;
    }
    /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Customer $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Customer $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Customer $task): bool
    {
        return $user->id === $task->user_id;
    }
}