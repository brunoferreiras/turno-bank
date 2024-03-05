<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\UserRepository;

class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepository
{
    public function model()
    {
        return User::class;
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }
}
