<?php

namespace App\Http\Controllers\User\Repositories;

use App\Http\Controllers\User\Models\User;

readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param \App\Http\Controllers\User\Models\User $model
     */
    public function __construct(
        protected User $model
    )
    {
    }

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data): User
    {
        return tap($user)->update([
            'name' => $data['name'],
        ]);
    }
}