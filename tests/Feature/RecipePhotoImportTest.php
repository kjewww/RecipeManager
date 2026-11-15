<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Cookbook;
use App\Models\Food;
use App\Models\IngredientAttribute;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\User;
use App\Services\RecipeImport\RecipeImporter;
use App\Services\RecipeImport\RecipeJsonSchema;
use App\Services\RecipeImport\RecipeJsonValidator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Characterization tests for photo import.
 *
 * They lock the behaviour at the import boundary (JSON in, files on the
 * "recipes" disk out) so the internal representation of parsed photos can be
 * refactored without changing what the user ends up with.
 */
class RecipePhotoImportTest extends TestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private function pngBinary(): string
    {
        return (string) base64_decode(self::PNG, true);
    }

    private function pngDataUri(): string
    {
        return 'data:image/png;base64,'.self::PNG;
    }

    private function seedLookups(): void
    {
        Category::factory()->create(['name' => 'Dessert']);
        Unit::factory()->create(['name' => 'g', 'name_shortcut' => 'g']);
        Unit::factory()->create(['name' => 'Prise', 'name_shortcut' => null]);

        foreach (['Mehl', 'Butter', 'Margarine', 'Apfel', 'Salz'] as $food) {
            Food::factory()->create(['name' => $food]);
        }

        foreach (['gesiebt', 'weich', 'säuerlich', 'geschält'] as $attribute) {
            IngredientAttribute::factory()->create(['name' => $attribute]);
        }

        foreach (['Kuchen', 'Herbst'] as $tag) {
            Tag::factory()->create(['name' => $tag]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $photos
     */
    private function import(array $photos): Recipe
    {
        $this->seedLookups();

        $user = User::factory()->admin()->create();
        Cookbook::factory()->create(['name' => 'Omas Backbuch', 'author_id' => $user->author_id]);

        $json = (string) json_encode(array_merge(RecipeJsonSchema::example(), ['photos' => $photos]));

        $parsed = app(RecipeJsonValidator::class)->validate($json);

        return app(RecipeImporter::class)->import($parsed, $user);
    }

    public function test_it_stores_an_imported_photo_under_a_slugged_filename(): void
    {
        Storage::fake('recipes');

        $recipe = $this->import([
            ['filename' => 'Apfel Foto.png', 'data' => $this->pngDataUri()],
        ]);

        $path = $recipe->getKey().'/apfel-foto.png';

        $this->assertSame(
            ['apfel-foto.png'],
            $recipe->photos->map(fn ($document) => $document->name())->all()
        );
        Storage::disk('recipes')->assertExists($path);
        $this->assertSame($this->pngBinary(), Storage::disk('recipes')->get($path));
    }

    public function test_it_falls_back_to_the_recipe_name_when_a_photo_has_no_filename(): void
    {
        Storage::fake('recipes');

        $recipe = $this->import([
            ['filename' => '', 'data' => $this->pngDataUri()],
        ]);

        $this->assertSame(
            ['apfelkuchen-1.png'],
            $recipe->photos->map(fn ($document) => $document->name())->all()
        );
        Storage::disk('recipes')->assertExists($recipe->getKey().'/apfelkuchen-1.png');
    }

    public function test_it_skips_photo_data_that_is_not_a_supported_image(): void
    {
        Storage::fake('recipes');

        $recipe = $this->import([
            ['filename' => 'not-an-image.txt', 'data' => 'data:text/plain;base64,aGk='],
        ]);

        $this->assertTrue($recipe->photos->isEmpty());
        $this->assertSame([], Storage::disk('recipes')->allFiles());
    }
}
