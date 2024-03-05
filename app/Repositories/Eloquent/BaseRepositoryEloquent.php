<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepositoryEloquent implements BaseRepository
{
    protected Application $app;
    protected Model $model;

    public function __construct(
        Application $app
    ) {
        $this->app = $app;
        $this->makeModel();
    }

    abstract public function model();

    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $model;
    }

    public function create(array $attributes): Model
    {
        return $this->makeModel()->create($attributes);
    }
}
