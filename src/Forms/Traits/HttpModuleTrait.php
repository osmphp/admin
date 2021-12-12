<?php

namespace Osm\Admin\Forms\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Framework\Http\Module;
use function Osm\merge;

trait HttpModuleTrait
{
    protected function around_loadRoutes(callable $proceed): void {
        /* @var Module|static $this */

        global $osm_app; /* @var App $osm_app */

        // first, find all statically defined routes, and put them into
        // `routes` and `dynamic_routes` properties. Note that these
        // properties are `#Cached`, so this only happens once
        $proceed();

        foreach ($osm_app->schema->classes as $class) {
            foreach ($class->forms as $form) {
                $this->routes = merge($this->routes, $form->routes);
            }
        }
    }
}