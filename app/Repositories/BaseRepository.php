<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    public function create(array $data): Model;
}
