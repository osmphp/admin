<?php

declare(strict_types=1);

return (object)[
    'admin_title' => 'Osm Admin',

    /* @see \Osm\Framework\Logs\Hints\LogSettings */
    'logs' => (object)[
        'migrations' => (bool)($_ENV['LOG_MIGRATIONS'] ?? false),
    ],
];