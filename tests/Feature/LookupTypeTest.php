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
use Tests\TestCase;

class LookupTypeTest extends TestCase
{
    public function test_every_type_maps_to_its_model(): void
    {
        $expected = [
            'food' => Food::class,
            'unit' => Unit::class,
            'tag' => Tag::class,
            'category' => Category::class,
            'cookbook' => Cookbook::class,
            'attribute' => IngredientAttribute::class,
            'author' => Author::class,
        ];

        foreach (LookupType::cases() as $type) {
            $this->assertSame($expected[$type->value], $type->model(), $type->value);
        }
    }

    public function test_every_type_has_a_label_and_a_max_length(): void
    {
        foreach (LookupType::cases() as $type) {
            $this->assertNotSame('', $type->label(), $type->value);
            $this->assertGreaterThan(0, $type->maxLength(), $type->value);
        }
    }

    public function test_only_unit_tag_and_attribute_are_optional(): void
    {
        $optional = [LookupType::Unit, LookupType::Tag, LookupType::Attribute];

        foreach (LookupType::cases() as $type) {
            $this->assertSame(in_array($type, $optional, true), $type->isOptional(), $type->value);
        }
    }

    public function test_every_type_is_blocked_for_guests(): void
    {
        foreach (LookupType::cases() as $type) {
            $this->assertFalse($type->canBeCreatedBy(null), $type->value);
        }
    }

    public function test_author_is_never_created_on_the_fly(): void
    {
        $admin = User::factory()->create(['admin' => true]);

        $this->assertFalse(LookupType::Author->canBeCreatedBy($admin));
    }

    public function test_cookbook_and_category_may_be_created_by_everyone(): void
    {
        $member = User::factory()->create(['admin' => false]);

        $this->assertTrue(LookupType::Cookbook->canBeCreatedBy($member));
        $this->assertTrue(LookupType::Category->canBeCreatedBy($member));
    }

    public function test_the_remaining_types_are_admin_only(): void
    {
        $admin = User::factory()->create(['admin' => true]);
        $member = User::factory()->create(['admin' => false]);

        foreach ([LookupType::Food, LookupType::Unit, LookupType::Tag, LookupType::Attribute] as $type) {
            $this->assertFalse($type->canBeCreatedBy($member), $type->value);
            $this->assertTrue($type->canBeCreatedBy($admin), $type->value);
        }
    }

    public function test_query_includes_trashed_rows(): void
    {
        $food = Food::factory()->create(['name' => 'Trashed food']);
        $food->delete();

        $this->assertTrue(
            LookupType::Food->query()->whereKey($food->getKey())->exists()
        );
    }
}
