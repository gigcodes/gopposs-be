<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Browser;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->save();

            $user->assignRole('user');

            event(new Registered($user));

            $device = Browser::platformName() . ' / ' . Browser::browserName();

            $sanctumToken = $user->createToken($device);

            DB::commit();

            return $this->respondWithSuccess([
                'token' => $sanctumToken->plainTextToken,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception->getMessage());
        }

    }

    /**
     * Generate sanctum token on successful login
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        $request->authenticate($user);

        $browser = Browser::parse($request->userAgent());
        $device = $browser->platformName() . ' / ' . $browser->browserName();

        $sanctumToken = $user->createToken(
            $device,
            ['*'],
            $request->remember ?
                now()->addMonth() :
                now()->addDay()
        );

        $sanctumToken->accessToken->ip = $request->ip();
        $sanctumToken->accessToken->save();

        return $this->respondWithSuccess([
            'token' => $sanctumToken->plainTextToken,
        ]);
    }

    /**
     * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondNoContent();
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->respondWithSuccess([
            'user' => [
                ...$user->toArray(),
                'must_verify_email' => $user->mustVerifyEmail(),
                'roles' => $user->roles()->select('name')->pluck('name'),
                'providers' => $user->userProviders()->select('name')->pluck('name'),
            ],
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     * @throws ValidationException
     */
    public function sendResetPasswordLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $this->respondOk(__($status));
    }

    /**
     * Handle an incoming new password request.
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            static function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                    'has_password' => true,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $this->respondOk(__($status));
    }

    /**
     * Get authenticated user devices
     */
    public function devices(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = $user->tokens()
            ->select('id', 'name', 'ip', 'last_used_at')
            ->orderBy('last_used_at', 'DESC')
            ->get();

        $currentToken = $user->currentAccessToken();

        foreach ($devices as $device) {
            $device->hash = Crypt::encryptString($device->id);

            if ($currentToken->id === $device->id) {
                $device->is_current = true;
            }

            unset($device->id);
        }

        return $this->respondWithSuccess(['devices' => $devices]);
    }

    /**
     * Revoke token by id
     */
    public function deviceDisconnect(Request $request): JsonResponse
    {
        $request->validate([
            'hash' => 'required',
        ]);

        $user = $request->user();

        $id = (int)Crypt::decryptString($request->hash);

        if (!empty($id)) {
            $user->tokens()->where('id', $id)->delete();
        }

        return $this->respondNoContent();
    }
}
