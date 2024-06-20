<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class OtpVerificationCodeResentEvent
{
    use Dispatchable;

    public function __construct(protected User $user)
    {
    }
}
