<?php

namespace App\Http\Controllers;

use App\Events\Verified;
use App\Exceptions\InvalidTokenException;
use App\Http\Requests\VerificationRequest;
use App\Models\User;
use App\Services\Tokens\TokenBrokerInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class TokenVerificationController extends Controller implements HasMiddleware
{

    public function __construct(private readonly TokenBrokerInterface $tokenBroker)
    {
    }

    public static function middleware(): array
    {
        $throttle = config('verification.throttle');
        return ["throttle:$throttle,1"];
    }

    public function verify(VerificationRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->respondFailedValidation('Email already verified.');
        }

        try {
            $this->tokenBroker->verifyToken($user, $request->token);
        } catch (InvalidTokenException $e) {
            return $this->respondError($e->getMessage());
        }

        event(new Verified($user, $request->all()));

        return $this->respondOk(__('verification.successful_verification'));
    }

    public function resend(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->respondFailedValidation('Email already verified.');
        }

        $this->tokenBroker->sendToken($user);

        return $this->respondOk(__('verification.successful_resend'));
    }
}
