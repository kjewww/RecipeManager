<?php

namespace App\Services\RecipeImport;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Describes one kind of lookup table used by the recipe importer. Every subtype
 * only fills in what makes it different; the shared default lives here.
 */
abstract class LookupDefinition
{
    /**
     * @return class-string<Model>
     */
    abstract public function model(): string;

    /**
     * A query including trashed rows, which still occupy the unique name index.
     *
     * @return Builder<covariant Model>
     */
    abstract public function query(): Builder;

    abstract public function label(): string;

    abstract public function maxLength(): int;

    /**
     * Whether the value may simply be dropped: units are nullable on an
     * ingredient, and attributes and tags are pivot rows.
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * Mirrors the create permissions of RecipeForm: everyone may add cookbooks
     * and categories on the fly, the remaining lookup tables are admin only.
     */
    public function canBeCreatedBy(?User $user): bool
    {
        return $user !== null && ($this->creatableByEveryone() || (bool) $user->admin);
    }

    protected function creatableByEveryone(): bool
    {
        return false;
    }
}
