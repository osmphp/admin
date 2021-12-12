<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Class_ $class
 * @property string $url #[Serialized]
 */
class Interface_ extends Object_
{
    use SubTypes;

    public function route(string $routeName): string
    {
        $pos = strpos($routeName, ' ') + 1;

        return substr($routeName, 0, $pos) . $this->url .
            substr($routeName, $pos);
    }

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        throw new Required(__METHOD__);
    }

    protected function get_area_class_name(): string {
        throw new Required(__METHOD__);
    }
}