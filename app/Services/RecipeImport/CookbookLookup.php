<?php

namespace App\Services\RecipeImport;

use App\Models\Cookbook;
use Illuminate\Database\Eloquent\Builder;

final class CookbookLookup extends LookupDefinition
{
    public function model(): string
    {
        return Cookbook::class;
    }

    /**
     * @return Builder<Cookbook>
     */
    public function query(): Builder
    {
        return Cookbook::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Cookbook');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_COOKBOOK_NAME;
    }

    protected function creatableByEveryone(): bool
    {
        return true;
    }
}
