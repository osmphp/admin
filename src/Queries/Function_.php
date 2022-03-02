<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Schema\DataType;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;

/**
 * Dependencies:
 *
 * @property DataType[] $data_types
 */
class Function_ extends Object_
{
    use SubTypes;

    public function resolve(Formula\Call $call): void {
        throw new NotImplemented($this);
    }

    public function toSql(Formula\Call $call, array &$bindings,
        array &$from, string $join): string
    {
        throw new NotImplemented($this);
    }

    protected function get_data_types(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[\Osm\Admin\Schema\Module::class]->data_types;
    }
}