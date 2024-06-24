<?php

namespace App\Contracts;

interface MustVerifyEmail
{
    public function hasVerifiedEmail(): bool;

    public function markEmailAsVerified(): bool;

    public function customSendEmailVerificationNotification(string $token): void;

    public function getEmailForVerification(): string;

}
