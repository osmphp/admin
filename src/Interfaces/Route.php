<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Http\Route as BaseRoute;

/**
 * @property string $class_name
 * @property string $interface_type
 * @property Class_ $class
 * @property Interface_ $interface
 */
class Route extends BaseRoute
{
    protected function get_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_interface_type(): string {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->class_name];
    }

    protected function get_interface(): Interface_ {
        return $this->class->interfaces[$this->interface_type];
    }
}