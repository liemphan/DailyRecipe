<?php

namespace DailyRecipe\Auth\Access;

use DailyRecipe\Auth\User;
use DailyRecipe\Notifications\UserInvite;

class UserInviteService extends UserTokenService
{
    protected $tokenTable = 'user_invites';
    protected $expiryTime = 336; // Two weeks

    /**
     * Send an invitation to a user to sign into DailyRecipe
     * Removes existing invitation tokens.
     *
     * @param User $user
     */
    public function sendInvitation(User $user)
    {
        $this->deleteByUser($user);
        $token = $this->createTokenForUser($user);
        $user->notify(new UserInvite($token));
    }
}
