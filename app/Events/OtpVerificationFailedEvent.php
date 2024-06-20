<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class OtpVerificationFailedEvent
{
    use Dispatchable;

    public function __construct(protected readonly User $user)
    {
    }
}
