<?php

namespace App\Services\RecipeImport;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

final class TagLookup extends LookupDefinition
{
    public function model(): string
    {
        return Tag::class;
    }

    /**
     * @return Builder<Tag>
     */
    public function query(): Builder
    {
        return Tag::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Tag');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_TAG_NAME;
    }

    public function isOptional(): bool
    {
        return true;
    }
}
