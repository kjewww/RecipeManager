<?php

namespace App\ValueObjects;

use App\Models\CookbookMembership;

/**
 * The five permission flags that always travel together on a cookbook
 * membership or invitation.
 */
final class CookbookGrants
{
    /**
     * @param  array<string, bool>  $grants
     */
    private function __construct(private readonly array $grants) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $grants = [];

        foreach (CookbookMembership::GRANTS as $grant) {
            $grants[$grant] = (bool) ($data[$grant] ?? false);
        }

        // Read access is implied by every other grant.
        if (array_filter($grants)) {
            $grants['can_read'] = true;
        }

        return new self($grants);
    }

    /**
     * @return array<string, bool>
     */
    public function toArray(): array
    {
        return $this->grants;
    }

    public function allows(string $grant): bool
    {
        return (bool) ($this->grants[$grant] ?? false);
    }
}
