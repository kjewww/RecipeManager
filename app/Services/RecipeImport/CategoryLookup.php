<?php

namespace App\Services\RecipeImport;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

final class CategoryLookup extends LookupDefinition
{
    public function model(): string
    {
        return Category::class;
    }

    /**
     * @return Builder<Category>
     */
    public function query(): Builder
    {
        return Category::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Category');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_CATEGORY_NAME;
    }

    protected function creatableByEveryone(): bool
    {
        return true;
    }
}
