<?php

namespace App\Services\RecipeImport;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

enum LookupType: string
{
    case Food = 'food';
    case Unit = 'unit';
    case Tag = 'tag';
    case Category = 'category';
    case Cookbook = 'cookbook';
    case Attribute = 'attribute';
    case Author = 'author';

    /**
     * The only remaining match: pick the class that knows the rest.
     */
    public function definition(): LookupDefinition
    {
        return match ($this) {
            self::Food => new FoodLookup,
            self::Unit => new UnitLookup,
            self::Tag => new TagLookup,
            self::Category => new CategoryLookup,
            self::Cookbook => new CookbookLookup,
            self::Attribute => new AttributeLookup,
            self::Author => new AuthorLookup,
        };
    }

    /**
     * @return class-string<Model>
     */
    public function model(): string
    {
        return $this->definition()->model();
    }

    /**
     * A query including trashed rows, which still occupy the unique name index.
     *
     * @return Builder<covariant Model>
     */
    public function query(): Builder
    {
        return $this->definition()->query();
    }

    public function label(): string
    {
        return $this->definition()->label();
    }

    public function maxLength(): int
    {
        return $this->definition()->maxLength();
    }

    /**
     * Whether the value may simply be dropped: units are nullable on an ingredient, and
     * attributes and tags are pivot rows. A food or category is mandatory.
     */
    public function isOptional(): bool
    {
        return $this->definition()->isOptional();
    }

    /**
     * Mirrors the create permissions of RecipeForm: everyone may add cookbooks and
     * categories on the fly, the remaining lookup tables are admin only.
     */
    public function canBeCreatedBy(?User $user): bool
    {
        return $this->definition()->canBeCreatedBy($user);
    }
}
