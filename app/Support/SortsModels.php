<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SortsModels
{
    /**
     * @param  class-string<Model>  $model
     * @param  array<int, int|string>  $ids
     */
    public static function apply(string $model, array $ids): void
    {
        foreach ($ids as $index => $id) {
            $model::query()->whereKey($id)->update(['sort_order' => $index + 1]);
        }
    }
}
