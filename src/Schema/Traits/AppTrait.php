<?php

namespace Osm\Data\Schema\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Data\Schema\Schema;

/**
 * @property Schema $schema
 */
#[UseIn(App::class)]
trait AppTrait
{
    protected function get_schema(): Schema {
        return Schema::new();
    }
}