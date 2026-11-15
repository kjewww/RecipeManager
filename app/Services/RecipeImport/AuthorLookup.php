<?php

namespace App\Services\RecipeImport;

use App\Models\Author;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AuthorLookup extends LookupDefinition
{
    public function model(): string
    {
        return Author::class;
    }

    /**
     * @return Builder<Author>
     */
    public function query(): Builder
    {
        return Author::query()->withTrashed();
    }

    public function label(): string
    {
        return __('Author');
    }

    public function maxLength(): int
    {
        return RecipeJsonSchema::MAX_AUTHOR_NAME;
    }

    /**
     * Authors are never created on the fly, not even by an admin.
     */
    public function canBeCreatedBy(?User $user): bool
    {
        return false;
    }
}
