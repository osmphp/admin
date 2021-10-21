<?php

declare(strict_types=1);

namespace My\Samples;

use Osm\App\App as BaseApp;

class App extends BaseApp
{
    public static bool $load_dev_sections = true;
}