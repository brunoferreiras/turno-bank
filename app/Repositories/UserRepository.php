<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepository extends BaseRepository
{
    public function register(array $data): User;
}
