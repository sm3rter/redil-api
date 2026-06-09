<?php

return [
    'allow_origins' => ['*'],
    'allow_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allow_headers' => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],
    'allow_credentials' => false,
    'max_age' => 600,
];
