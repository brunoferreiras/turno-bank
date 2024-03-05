<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    public function create(array $data)
    {
        return $this->userRepository->create($data);
    }
}
