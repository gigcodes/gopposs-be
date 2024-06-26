<?php

namespace App\Providers;

use App\Helpers\Image;
use App\Listeners\SendMobileVerificationNotification;
use App\Services\Tokens\TokenBroker;
use App\Services\Tokens\TokenBrokerInterface;
use App\Services\Tokens\TokenRepositoryInterface;
use App\Services\Tokens\TokenRepositoryManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Telescope only in local environment
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class) && $this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton('verifier.token.repository', fn ($app) => new TokenRepositoryManager($app));

        $this->app->singleton(TokenRepositoryInterface::class,
            fn ($app) => $app['verifier.token.repository']->driver()
        );

        $this->app->bind(TokenBrokerInterface::class, TokenBroker::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', static function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('uploads', static function (Request $request) {
            return $request->user()?->hasRole('admin')
                ? Limit::none()
                : Limit::perMinute(10)->by($request->ip());
        });

        ResetPassword::createUrlUsing(static function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/auth/reset/{$token}?email={$notifiable->getEmailForPasswordReset()}";
        });

        Event::listen(
            Registered::class,
            SendMobileVerificationNotification::class,
        );

        /**
         * Convert uploaded image to webp, jpeg or png format and resize it
         */
        UploadedFile::macro('convert', function (?int $width = null, ?int $height = null, string $extension = 'webp', int $quality = 90): UploadedFile {
            return tap($this, static function (UploadedFile $file) use ($width, $height, $extension, $quality) {
                Image::convert($file->path(), $file->path(), $width, $height, $extension, $quality);
            });
        });

        /**
         * Remove all special characters from a string
         */
        Str::macro('onlyWords', static function (string $text): string {
            // \p{L} matches any kind of letter from any language
            // \d matches a digit in any script
            return Str::replaceMatches('/[^\p{L}\d ]/u', '', $text);
        });
    }
}
