<?php

namespace App\Services\Tokens;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Manager;

class TokenRepositoryManager extends Manager
{
    protected function createCacheDriver(): TokenRepositoryInterface
    {
        return new CacheTokenRepository(
            $this->config->get('verification.token_lifetime', 5),
            $this->config->get('verification.token_length', 5)
        );
    }

    protected function createDatabaseDriver(): TokenRepositoryInterface
    {
        return new DatabaseTokenRepository(
            $this->config->get('verification.token_lifetime', 5),
            $this->config->get('verification.token_length', 5),
            $this->config->get('verification.token_table', 'verification_tokens'),
            $this->container->make(ConnectionInterface::class)
        );
    }

    public function getDefaultDriver()
    {
        return $this->config->get('verification.token_storage', 'cache');
    }
}
