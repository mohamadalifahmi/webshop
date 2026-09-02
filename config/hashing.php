<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | bcrypt is the recommended driver (adaptive, memory-hard per iteration,
    | industry default). Explicitly pinned to prevent accidental fallback to a
    | weaker driver in any environment.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        // Laravel 11 no longer requires the verify/salt keys; kept for clarity.
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon2 Options
    |--------------------------------------------------------------------------
    */

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
    ],

];