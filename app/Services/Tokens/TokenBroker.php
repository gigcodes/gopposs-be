<?php

namespace App\Services\Tokens;

use App\Contracts\MustVerifyEmail;
use App\Exceptions\InvalidTokenException;

class TokenBroker implements TokenBrokerInterface
{
    public function __construct(protected TokenRepositoryInterface $tokenRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function sendToken(MustVerifyEmail $user): void
    {
        $user->customSendEmailVerificationNotification($this->tokenRepository->create($user));
    }

    /**
     * {@inheritdoc}
     */
    public function verifyToken(MustVerifyEmail $user, string $token): bool
    {
        throw_unless($this->tokenExists($user, $token), InvalidTokenException::class);

        $user->markEmailAsVerified();

        $this->tokenRepository->deleteExisting($user);

        return true;
    }

    public function tokenExists(MustVerifyEmail $user, string $token): bool
    {
        return $this->tokenRepository->exists($user, $token);
    }

    public function getLatestSentAt(MustVerifyEmail $user, string $token): string
    {
        return $this->tokenRepository->latestSentAt($user, $token);
    }
}
