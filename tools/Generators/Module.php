<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Schema\Hydrator;
use Osm\Data\Schema\Reflector;
use Osm\Data\Schema\Schema;
use Osm\Runtime\Apps;
use Osm\Tools\App;

/**
 * @property Schema $schema
 */
class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Tools\Base\Module::class,
    ];

    protected function get_schema(): Schema {
        $reflection = Apps::run(Apps::create(
            \Osm\Project\App::class),
            fn() => Reflector::new()->reflect()
        );

        return Hydrator::new(['reflection' => $reflection])->hydrate();
    }
}