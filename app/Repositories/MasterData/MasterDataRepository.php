<?php

namespace App\Repositories\MasterData;

use Illuminate\Database\Eloquent\Model;

class MasterDataRepository
{
    public function create(string $modelClass, array $attributes): Model
    {
        return $modelClass::query()->create($attributes);
    }

    public function findById(string $modelClass, int $id): ?Model
    {
        return $modelClass::query()->whereKey($id)->first();
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}
