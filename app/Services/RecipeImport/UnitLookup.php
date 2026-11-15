<?php

namespace App\Services\RecipeImport;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;

final class UnitLookup extends LookupDefinition
{
    public function model(): string
    {
        return Unit::class;
    }

    /**
     * @return Builder<Unit>
     */
    public function query(): Builder
    {
        return Unit::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Unit');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_UNIT_NAME;
    }

    public function isOptional(): bool
    {
        return true;
    }
}
