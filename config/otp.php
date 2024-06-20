<?php

return [
    /**
     * Whether the package will register the routes and listeners.
     */
    'enabled' => false,

    /**
     * The notification to be sent to the logged-in user.
     * Override this with your own implementation so that
     * you can customize the channels, message format etc.
     */
    'notification' => \App\Notifications\SentOtpUserNotification::class,
];
