<?php

namespace Tests\Feature;

use App\Services\Document;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentDataUriTest extends TestCase
{
    /**
     * A 1x1 transparent PNG.
     */
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public function test_an_existing_file_becomes_a_base64_data_uri(): void
    {
        Storage::fake('recipes');
        Storage::disk('recipes')->put('1/photo.png', self::png());

        $uri = (new Document('1/photo.png', 'recipes'))->dataUri();

        $this->assertNotNull($uri);
        $this->assertStringStartsWith('data:image/png;base64,', $uri);
        $this->assertSame(
            base64_encode(self::png()),
            substr($uri, strlen('data:image/png;base64,'))
        );
    }

    public function test_a_missing_file_returns_null(): void
    {
        Storage::fake('recipes');

        $this->assertNull((new Document('1/missing.png', 'recipes'))->dataUri());
    }

    private static function png(): string
    {
        $binary = base64_decode(self::PNG, true);

        if ($binary === false) {
            self::fail('The PNG fixture is not valid base64.');
        }

        return $binary;
    }
}
