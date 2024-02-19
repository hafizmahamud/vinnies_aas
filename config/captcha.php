<?php

return [
    'secret' => env('RECAPTCHA_PRIVATE_KEY'),
    'sitekey' => env('RECAPTCHA_PUBLIC_KEY'),
    'options' => [
        'timeout' => 30,
    ],
];
