<?php
declare(strict_types=1);

namespace App\Services\Tokens;

use App\Contracts\MustVerifyEmail;
use Exception;

interface TokenRepositoryInterface
{
    /**
     * Create a new token record.
     *
     * @throws Exception
     */
    public function create(MustVerifyEmail $user): string;

    /**
     * Determine if a token record exists and is valid.
     */
    public function exists(MustVerifyEmail $user, string $token): bool;

    /**
     * Delete all existing tokens from the database.
     */
    public function deleteExisting(MustVerifyEmail $user): void;

    public function latestSentAt(MustVerifyEmail $user, string $token): string;
}
