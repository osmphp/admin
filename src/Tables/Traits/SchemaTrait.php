<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Base\Attributes\Markers\Table\Column as ColumnMarker;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $table_column_type_names #[Serialized]
 */
#[UseIn(Schema::class)]
trait SchemaTrait
{
    protected function get_table_column_type_names(): array {
        global $osm_app; /* @var App $osm_app */

        $columnTypeNames = [];

        foreach ($osm_app->classes as $class) {
            if (!isset($class->attributes[\Attribute::class])) {
                continue;
            }

            /* @var ColumnMarker $column */
            if ($column  = $class->attributes[ColumnMarker::class] ?? null) {
                $columnTypeNames[$class->name] = $column->type;
            }

        }

        return $columnTypeNames;
    }
}