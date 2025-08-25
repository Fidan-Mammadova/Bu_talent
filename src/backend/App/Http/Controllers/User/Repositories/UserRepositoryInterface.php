<?php
namespace App\Http\Controllers\User\Repositories;

use App\Http\Controllers\User\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param array $data
     * @return \App\Http\Controllers\User\Models\User
     */
    public function create(array $data): User;

    /**
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User;

    /**
     * @param \App\Http\Controllers\User\Models\User $user
     * @param array $data
     * @return \App\Http\Controllers\User\Models\User
     */
    public function update(User $user, array $data): User;
}