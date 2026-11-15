<?php

namespace Tests\Feature;

use App\Filament\Resources\Recipes\Pages\CreateRecipe;
use App\Filament\Resources\Recipes\Schemas\IngredientSchema;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;
use Tests\TestCase;

class IngredientSchemaTest extends TestCase
{
    public function test_repeater_builds_a_named_repeater(): void
    {
        $repeater = IngredientSchema::repeater('ungroupedIngredients', 'Ingredients without group', ungrouped: true);

        $this->assertInstanceOf(Repeater::class, $repeater);
        $this->assertSame('ungroupedIngredients', $repeater->getName());
        $this->assertSame('Ingredients without group', $repeater->getLabel());
    }

    public function test_grouped_repeater_uses_its_own_name(): void
    {
        $repeater = IngredientSchema::repeater('topLevelIngredients', 'Ingredients');

        $this->assertSame('topLevelIngredients', $repeater->getName());
    }

    public function test_recipe_form_exposes_both_ingredient_repeaters(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreateRecipe::class)
            ->assertFormFieldExists('ungroupedIngredients')
            ->assertFormFieldExists('ingredientGroups');
    }
}
