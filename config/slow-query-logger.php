<?php

return [
    /**
     * log all sql queries that are slower than X seconds
     * laravel measures at a precision of 2 digits, so 0.7134 will be logged as 0.71
     */
    'time-to-log' => env('SLOW_QUERY_TIME', 0.7),

    /**
     * log when you are on these environments
     */
    'environments' => [
        env('SLOW_QUERY_ENABLED', false) ? env('APP_ENV') : ''
    ],

    /**
     * level to log
     */
    'log-level' => 'debug',
];
