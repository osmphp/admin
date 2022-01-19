<?php

namespace Osm\Admin\Filters;

use Osm\Core\BaseModule;

/**
 * @property string[] $operators
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Queries\Module::class,
        \Osm\Admin\Schema\Module::class,
    ];

    protected function get_operators(): array {
        return [
            '-' => Filter::NOT_EQUALS,
            '' => Filter::EQUALS,
        ];
    }
}