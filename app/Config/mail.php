<?php

/**
 * Mail configuration options.
 *
 * Changes to these config files are not supported by DailyRecipe and may break upon updates.
 * Configuration should be altered via the `.env` file or environment variables.
 * Do not edit this file unless you're happy to maintain any changes yourself.
 */

return [

    // Mail driver to use.
    // From Laravel 7+ this is MAIL_MAILER in laravel.
    // Kept as MAIL_DRIVER in DailyRecipe to prevent breaking change.
    // Options: smtp, sendmail, log, array
    'driver' => env('MAIL_DRIVER', 'smtp'),

    // SMTP host address
    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

    // SMTP host port
    'port' => env('MAIL_PORT', 587),

    // Global "From" address & name
    'from' => [
        'address' => env('MAIL_FROM', 'mail@dailyrecipeapp.com'),
        'name'    => env('MAIL_FROM_NAME', 'DailyRecipe'),
    ],

    // Email encryption protocol
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    // SMTP server username
    'username' => env('MAIL_USERNAME'),

    // SMTP server password
    'password' => env('MAIL_PASSWORD'),

    // Sendmail application path
    'sendmail' => '/usr/sbin/sendmail -bs',

    // Email markdown configuration
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    // Log Channel
    // If you are using the "log" driver, you may specify the logging channel
    // if you prefer to keep mail messages separate from other log entries
    // for simpler reading. Otherwise, the default channel will be used.
    'log_channel' => env('MAIL_LOG_CHANNEL'),

];
