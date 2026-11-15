<?php

namespace App\Services\RecipeImport;

use App\Models\Food;
use Illuminate\Database\Eloquent\Builder;

final class FoodLookup extends LookupDefinition
{
    public function model(): string
    {
        return Food::class;
    }

    /**
     * @return Builder<Food>
     */
    public function query(): Builder
    {
        return Food::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Food');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_FOOD_NAME;
    }
}
