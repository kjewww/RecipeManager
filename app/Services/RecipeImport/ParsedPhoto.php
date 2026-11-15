<?php

namespace App\Services\RecipeImport;

/**
 * A single photo from the imported JSON: a filename hint plus a base64 data URI.
 */
final class ParsedPhoto
{
    public function __construct(
        public readonly string $filename,
        public readonly string $data,
    ) {}

    /**
     * @param  array<string, mixed>  $photo
     */
    public static function fromArray(array $photo): ?self
    {
        $data = $photo['data'] ?? null;

        if (! is_string($data)) {
            return null;
        }

        return new self(
            filename: trim((string) ($photo['filename'] ?? '')),
            data: $data,
        );
    }
}
