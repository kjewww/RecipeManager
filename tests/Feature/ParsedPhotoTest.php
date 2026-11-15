<?php

namespace Tests\Feature;

use App\Services\RecipeImport\ParsedPhoto;
use App\Services\RecipeImport\ParsedRecipe;
use Tests\TestCase;

class ParsedPhotoTest extends TestCase
{
    public function test_from_array_keeps_the_filename_and_data(): void
    {
        $photo = ParsedPhoto::fromArray([
            'filename' => 'soup.jpg',
            'data' => 'data:image/jpeg;base64,AAA',
        ]);

        $this->assertInstanceOf(ParsedPhoto::class, $photo);
        $this->assertSame('soup.jpg', $photo->filename);
        $this->assertSame('data:image/jpeg;base64,AAA', $photo->data);
    }

    public function test_from_array_trims_the_filename(): void
    {
        $photo = ParsedPhoto::fromArray([
            'filename' => '  soup.jpg  ',
            'data' => 'data:image/jpeg;base64,AAA',
        ]);

        $this->assertSame('soup.jpg', $photo?->filename);
    }

    public function test_from_array_rejects_a_photo_without_data(): void
    {
        $this->assertNull(ParsedPhoto::fromArray(['filename' => 'soup.jpg']));
        $this->assertNull(ParsedPhoto::fromArray(['data' => 123]));
    }

    public function test_parsed_recipe_stores_photo_objects(): void
    {
        $recipe = ParsedRecipe::fromArray([
            'name' => 'Soup',
            'category' => 'Starter',
            'complexity' => 'simple',
            'instructions' => 'Cook it.',
            'photos' => [
                ['filename' => 'soup.jpg', 'data' => 'data:image/jpeg;base64,AAA'],
                ['filename' => 'invalid.jpg'],
            ],
        ]);

        $this->assertCount(1, $recipe->photos);
        $this->assertInstanceOf(ParsedPhoto::class, $recipe->photos[0]);
        $this->assertSame('soup.jpg', $recipe->photos[0]->filename);
    }
}
