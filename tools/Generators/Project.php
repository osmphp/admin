<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Runtime\Apps;

/**
 * @property ProjectModule[] $modules
 */
class Project extends Object_
{
    public static function fromReflection(\stdClass|Hints\Project $reflection)
        : static
    {
        $project = static::new((array)$reflection);

        $project->modules = array_map(
            fn(\stdClass|Hints\Module $module) =>
                ProjectModule::fromReflection($project, $module),
            $project->modules);

        return $project;
    }
}