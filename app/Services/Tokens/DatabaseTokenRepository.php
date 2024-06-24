<?php

namespace App\Services\Tokens;

use App\Contracts\MustVerifyEmail;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

class DatabaseTokenRepository extends AbstractTokenRepository
{
    public function __construct(
        protected int $expires,
        protected int $tokenLength,
        protected string $table,
        protected ConnectionInterface $connection
    ) {
        parent::__construct($expires, $tokenLength);
    }

    /**
     * {@inheritdoc}
     */
    public function create(MustVerifyEmail $user): string
    {
        $mobile = $user->getEmailForVerification();

        $this->deleteExisting($user);

        $token = $this->createNewToken();

        $this->insert($mobile, $token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteExisting(MustVerifyEmail $user): void
    {
        optional($this->getTable()->where('email', $user->getEmailForVerification()))->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(MustVerifyEmail $user, string $token): bool
    {
        $record = $this->getTokenRecord($user, $token);

        return $record && ! $this->tokenExpired($record['expires_at']);
    }

    public function latestSentAt(MustVerifyEmail $user, string $token): string
    {
        $tokenRow = $this->getTokenRecord($user, $token);

        if (! $tokenRow) {
            return '';
        }

        return $tokenRow['sent_at'];
    }

    /**
     * Begin a new database query against the table.
     */
    protected function getTable(): Builder
    {
        return $this->connection->table($this->table);
    }

    /**
     * @throws Exception
     */
    protected function insert(string $email, string $token): bool
    {
        return $this->getTable()->insert($this->getPayload($email, $token));
    }

    /**
     * @inheritDoc
     */
    protected function getPayload(string $email, string $token): array
    {
        return parent::getPayload($email, $token) + ['expires_at' => now()->addMinutes($this->expires)];
    }

    private function getTokenRecord(MustVerifyEmail $user, string $token): array
    {
        return (array) $this->getTable()
            ->where('email', $user->getEmailForVerification())
            ->where('token', $token)
            ->first();
    }
}
