<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Schema\Schema;
use Osm\Framework\Cache\Attributes\Cached;

/**
 * @property Schema $schema #[Cached('schema')]
 *
* @uses Cached
 */
#[UseIn(App::class)]
trait AppTrait
{
    protected function get_schema(): Schema {
        return Schema::new()->parse();
//        return hydrate(Schema::class, json_decode(
//            file_get_contents(__DIR__ . '/schema.json'),
//            flags: JSON_THROW_ON_ERROR));
    }
}