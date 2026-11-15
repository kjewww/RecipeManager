<?php

namespace App\ValueObjects;

use App\Models\User;

/**
 * Everything needed to share a cookbook with one address: the address itself,
 * the grants to hand out, and the user doing the sharing.
 */
final class CookbookInvitationData
{
    public function __construct(
        public readonly string $email,
        public readonly CookbookGrants $grants,
        public readonly User $invitedBy,
    ) {}
}
