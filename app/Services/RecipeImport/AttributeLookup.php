<?php

namespace App\Services\RecipeImport;

use App\Models\IngredientAttribute;
use Illuminate\Database\Eloquent\Builder;

final class AttributeLookup extends LookupDefinition
{
    public function model(): string
    {
        return IngredientAttribute::class;
    }

    /**
     * @return Builder<IngredientAttribute>
     */
    public function query(): Builder
    {
        return IngredientAttribute::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Ingredient attribute');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_ATTRIBUTE_NAME;
    }

    public function isOptional(): bool
    {
        return true;
    }
}
