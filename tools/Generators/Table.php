<?php

namespace Osm\Data\Tools\Generators;

use Osm\Core\Object_;

/**
 * @property ProjectModule $module
 */
class Table extends Object_
{

    public static function fromReflection(ProjectModule $module,
        Hints\Table|Table|\stdClass $reflection): static
    {
        $table = static::new((array)$reflection);
        $table->module = $module;

        return $table;
    }
}