<?php

namespace App\Services\Tokens;

use App\Contracts\MustVerifyEmail;
use Illuminate\Support\Facades\Cache;

class CacheTokenRepository extends AbstractTokenRepository
{
    public function create(MustVerifyEmail $user): string
    {
        $email = $user->getEmailForVerification();

        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $this->insert($email, $token);

        return $token;
    }

    public function deleteExisting(MustVerifyEmail $user): void
    {
        Cache::forget($user->getEmailForVerification());
    }

    public function exists(MustVerifyEmail $user, string $token): bool
    {
        return Cache::has($user->getEmailForVerification()) &&
            Cache::get($user->getEmailForVerification())['token'] === $token;
    }

    public function latestSentAt(MustVerifyEmail $user, string $token): string
    {
        $key = $user->getEmailForVerification();
        if (! Cache::has($key)) {
            return '';
        }

        return Cache::get($key)['sent_at'];
    }

    protected function insert(string $email, string $token): bool
    {
        return Cache::add($email, $this->getPayload($email, $token), now()->addMinutes($this->expires));
    }
}
