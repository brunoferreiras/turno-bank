<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    public function create(array $data): Model;

    public function findOne(int $id): ?Model;

    public function update(int $id, array $data);

    public function findWhere(array $where, array $columns = ['*']): ?Collection;
}
