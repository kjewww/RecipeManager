<?php

namespace App\Services;

use App\Mail\CookbookInvitationMail;
use App\Models\Cookbook;
use App\Models\CookbookInvitation;
use App\Models\User;
use App\ValueObjects\CookbookGrants;
use App\ValueObjects\CookbookInvitationData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CookbookSharing
{
    public const EXPIRES_AFTER_DAYS = 14;

    /**
     * Shares a cookbook with an email address. Existing users are added straight away,
     * everyone else receives an invitation.
     *
     * Returns nothing on purpose: the caller must not be able to tell whether the
     * address belongs to an account.
     */
    public function share(Cookbook $cookbook, CookbookInvitationData $invitation): void
    {
        $email = mb_strtolower(trim($invitation->email));

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user !== null) {
            $this->addMember($cookbook, $user, $invitation->grants);

            return;
        }

        $this->invite($cookbook, $invitation);
    }

    public function addMember(Cookbook $cookbook, User $user, CookbookGrants $grants): void
    {
        // The owner already holds every grant; a membership row would be meaningless.
        if ($cookbook->isOwnedBy($user)) {
            return;
        }

        $cookbook->members()->syncWithoutDetaching([
            $user->getKey() => $grants->toArray(),
        ]);
    }

    private function invite(Cookbook $cookbook, CookbookInvitationData $invitation): void
    {
        $token = CookbookInvitation::newToken();
        $email = mb_strtolower(trim($invitation->email));

        $invitationModel = $cookbook->invitations()->updateOrCreate(
            ['email' => $email],
            [
                ...$invitation->grants->toArray(),
                'invited_by_user_id' => $invitation->invitedBy->getKey(),
                'token_hash' => CookbookInvitation::hash($token),
                'expires_at' => now()->addDays(self::EXPIRES_AFTER_DAYS),
                'accepted_at' => null,
            ],
        );

        Mail::to($email)->send(new CookbookInvitationMail($invitationModel, $token));
    }

    /**
     * Binds a pending invitation to a user whose email matches it.
     */
    public function accept(CookbookInvitation $invitation, User $user): bool
    {
        // Possession of the token is not enough; the account must own the address.
        if (! $invitation->isPending() || mb_strtolower($user->email) !== mb_strtolower($invitation->email)) {
            return false;
        }

        DB::transaction(function () use ($invitation, $user): void {
            $this->addMember(
                $invitation->cookbook,
                $user,
                CookbookGrants::fromArray($invitation->grants()),
            );

            $invitation->forceFill(['accepted_at' => now()])->save();
        });

        return true;
    }

    /**
     * Binds every pending invitation for a freshly registered address.
     */
    public function acceptPendingFor(User $user): void
    {
        CookbookInvitation::query()
            ->pending()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($user->email)])
            ->with('cookbook')
            ->each(fn (CookbookInvitation $invitation) => $this->accept($invitation, $user));
    }
}
