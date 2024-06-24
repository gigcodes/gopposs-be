<?php
declare(strict_types=1);

namespace App\Services\Tokens;

use App\Contracts\MustVerifyEmail;
use Exception;
use Throwable;

interface TokenBrokerInterface
{
    /**
     * Send token via notification.
     *
     * @throws Exception
     */
    public function sendToken(MustVerifyEmail $user): void;

    /**
     * @throws Throwable
     */
    public function verifyToken(MustVerifyEmail $user, string $token): bool;

    public function tokenExists(MustVerifyEmail $user, string $token): bool;

    public function getLatestSentAt(MustVerifyEmail $user, string $token): string;
}
