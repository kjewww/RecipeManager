<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\Cookbook;
use App\Models\Food;
use App\Models\IngredientAttribute;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\User;
use App\Services\RecipeImport\LookupType;
use App\Services\RecipeImport\RecipeJsonSchema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Pins the behaviour of LookupType down, so splitting it into one class per lookup
 * (Replace Conditional with Polymorphism) cannot change any of it unnoticed.
 */
class LookupTypeTest extends TestCase
{
    /**
     * One row per lookup type: the type itself, its model, label, max name length,
     * whether it is optional, and whether an admin / a non-admin may create it.
     *
     * @return array<string, array{0: LookupType, 1: class-string, 2: string, 3: int, 4: bool, 5: bool, 6: bool}>
     */
    public static function types(): array
    {
        return [
            'food' => [LookupType::Food, Food::class, 'Food', RecipeJsonSchema::MAX_FOOD_NAME, false, true, false],
            'unit' => [LookupType::Unit, Unit::class, 'Unit', RecipeJsonSchema::MAX_UNIT_NAME, true, true, false],
            'tag' => [LookupType::Tag, Tag::class, 'Tag', RecipeJsonSchema::MAX_TAG_NAME, true, true, false],
            'category' => [LookupType::Category, Category::class, 'Category', RecipeJsonSchema::MAX_CATEGORY_NAME, false, true, true],
            'cookbook' => [LookupType::Cookbook, Cookbook::class, 'Cookbook', RecipeJsonSchema::MAX_COOKBOOK_NAME, false, true, true],
            'attribute' => [LookupType::Attribute, IngredientAttribute::class, 'Ingredient attribute', RecipeJsonSchema::MAX_ATTRIBUTE_NAME, true, true, false],
            'author' => [LookupType::Author, Author::class, 'Author', RecipeJsonSchema::MAX_AUTHOR_NAME, false, false, false],
        ];
    }

    #[DataProvider('types')]
    public function test_it_maps_each_type_to_its_model(LookupType $type, string $model): void
    {
        $this->assertSame($model, $type->model());
    }

    #[DataProvider('types')]
    public function test_it_labels_each_type(LookupType $type, string $model, string $label): void
    {
        $this->assertSame($label, $type->label());
    }

    #[DataProvider('types')]
    public function test_it_uses_each_types_max_name_length(LookupType $type, string $model, string $label, int $maxLength): void
    {
        $this->assertSame($maxLength, $type->maxLength());
    }

    #[DataProvider('types')]
    public function test_it_knows_which_types_are_optional(LookupType $type, string $model, string $label, int $maxLength, bool $optional): void
    {
        $this->assertSame($optional, $type->isOptional());
    }

    #[DataProvider('types')]
    public function test_it_knows_who_may_create_each_type(
        LookupType $type,
        string $model,
        string $label,
        int $maxLength,
        bool $optional,
        bool $adminCanCreate,
        bool $nonAdminCanCreate,
    ): void {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create(['admin' => false]);

        $this->assertSame($adminCanCreate, $type->canBeCreatedBy($admin));
        $this->assertSame($nonAdminCanCreate, $type->canBeCreatedBy($nonAdmin));
    }

    public function test_a_guest_may_never_create_a_lookup(): void
    {
        foreach (LookupType::cases() as $type) {
            $this->assertFalse($type->canBeCreatedBy(null), $type->name);
        }
    }

    /**
     * @param  class-string  $model
     */
    #[DataProvider('types')]
    public function test_the_lookup_query_includes_soft_deleted_rows(LookupType $type, string $model): void
    {
        $record = $model::factory()->create();
        $record->delete();

        $this->assertTrue($type->query()->whereKey($record->getKey())->exists());
        $this->assertFalse($model::query()->whereKey($record->getKey())->exists());
    }
}
