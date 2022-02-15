<?php

namespace Osm\Admin\Queries;

use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\BaseModule;

/**
 * @property Function_[] $functions
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Schema\Module::class,
    ];

    protected function get_functions(): array {
        global $osm_app; /* @var App $osm_app */

        $classNames = $osm_app->descendants->byName(Function_::class,
            Type::class);

        return array_map(function(string $className) {
            $new = "{$className}::new";

            return $new();
        }, $classNames);
    }
}