<?php

namespace App\Actions;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ValidateOtpAction
{
    public function handle(Request $request): bool
    {
        $this->validate($request);

        /** @var User $user */
        $user = auth()->user();

        $service = app(OtpService::class);

        $key = $service->generateKey($user);

        if ($service->check($user, $request->get('otp-code'))) {
            Session::put($service->generateVerifiedKey($user), true);
            Session::forget($key);

            return true;
        }

        return false;
    }

    public function validate(Request $request): void
    {
        $request->validate([
           'otp' => 'required|max:6|min:6|numeric',
        ]);
    }
}
