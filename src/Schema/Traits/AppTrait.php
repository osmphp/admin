<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Schema\Schema;
use Osm\Framework\Cache\Attributes\Cached;

/**
 * @property Schema $schema #[Cached('schema')]
 */
#[UseIn(App::class)]
trait AppTrait
{
    protected function get_schema(): Schema {
        return Schema::new();
    }
}