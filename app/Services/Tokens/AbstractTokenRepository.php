<?php

namespace App\Services\Tokens;

use Carbon\Carbon;
use Exception;

abstract class AbstractTokenRepository implements TokenRepositoryInterface
{
    public function __construct(protected int $expires, protected int $tokenLength)
    {
    }

    public function setExpires(int $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function createNewToken(): string
    {
        $tokenLength = config('verification.token_length', 5);

        return (string) random_int(10 ** ($tokenLength - 1), (10 ** $tokenLength) - 1);
    }

    protected function tokenExpired(string $expiresAt): bool
    {
        return Carbon::parse($expiresAt)->isPast();
    }

    /**
     * Build the record payload for the table.
     */
    protected function getPayload(string $email, string $token): array
    {
        return ['email' => $email, 'token' => $token, 'sent_at' => now()->toDateTimeString()];
    }

    /**
     * Insert into token storage.
     */
    abstract protected function insert(string $email, string $token): bool;
}
