<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Admin\Samples\Generics\Detail;
use Osm\Admin\Samples\Generics\Item;
use Osm\Admin\Samples\Generics\Master;
use Osm\Admin\Samples\Generics\TreeItem;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Schema\Schema;
use Osm\Framework\Cache\Attributes\Cached;
use function Osm\hydrate;

/**
 * @property Schema $schema #[Cached('schema')]
 */
#[UseIn(App::class)]
trait AppTrait
{
    protected function get_schema(): Schema {
        return hydrate(Schema::class, json_decode(
            file_get_contents(__DIR__ . '/schema.json'),
            flags: JSON_THROW_ON_ERROR));
    }
}