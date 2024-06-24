<?php

namespace App\Listeners;

use App\Contracts\MustVerifyEmail;
use App\Services\Tokens\TokenBrokerInterface;

class SendMobileVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(protected TokenBrokerInterface $tokenBroker)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $this->tokenBroker->sendToken($user);
        }
    }
}
