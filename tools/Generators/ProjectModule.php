<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property Project $project
 * @property string $name
 * @property string $path
 * @property string $namespace
 * @property Table[] $tables
 */
class ProjectModule extends Object_
{
    public static function fromReflection(Project $project,
        \stdClass|Hints\Module $reflection): static
    {
        $module = static::new((array)$reflection);
        $module->project = $project;

        $module->tables = array_map(
            fn(\stdClass|Hints\Table $table) =>
                Table::fromReflection($module, $table),
            $module->tables);

        return $module;
    }
}