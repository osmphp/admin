<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Framework\Logs\Hints\LogSettings;

/**
 * @property ?bool $migrations
 */
#[UseIn(LogSettings::class)]
trait LogSettingsTrait
{

}