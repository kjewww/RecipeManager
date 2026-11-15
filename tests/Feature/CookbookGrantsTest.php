<?php

namespace Tests\Feature;

use App\Models\CookbookMembership;
use App\ValueObjects\CookbookGrants;
use Tests\TestCase;

class CookbookGrantsTest extends TestCase
{
    public function test_missing_flags_default_to_false(): void
    {
        $grants = CookbookGrants::fromArray([])->toArray();

        $this->assertSame(array_fill_keys(CookbookMembership::GRANTS, false), $grants);
    }

    public function test_any_grant_implies_read_access(): void
    {
        foreach (['can_admin', 'can_create', 'can_update', 'can_delete'] as $grant) {
            $grants = CookbookGrants::fromArray([$grant => true]);

            $this->assertTrue($grants->allows($grant), $grant);
            $this->assertTrue($grants->allows('can_read'), $grant);
        }
    }

    public function test_no_grant_means_no_read_access(): void
    {
        $grants = CookbookGrants::fromArray(['can_read' => false]);

        $this->assertFalse($grants->allows('can_read'));
    }

    public function test_it_only_carries_the_known_flags(): void
    {
        $grants = CookbookGrants::fromArray(['can_read' => true, 'is_owner' => true])->toArray();

        $this->assertSame(CookbookMembership::GRANTS, array_keys($grants));
        $this->assertArrayNotHasKey('is_owner', $grants);
    }

    public function test_unknown_flags_are_not_allowed(): void
    {
        $this->assertFalse(CookbookGrants::fromArray(['can_read' => true])->allows('is_owner'));
    }
}
