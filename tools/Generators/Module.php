<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\BaseModule;
use Osm\Runtime\Apps;
use Osm\Tools\App;

/**
 * @property Project $project
 */
class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Tools\Base\Module::class,
    ];

    protected function get_project(): Project {
        $reflection = Apps::run(Apps::create(
            \Osm\Project\App::class),
            fn() => Reflector::new()->reflect()
        );

        return Project::fromReflection($reflection);
    }
}