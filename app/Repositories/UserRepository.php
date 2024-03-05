<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepository
{
    public function register(array $data): User;
}
