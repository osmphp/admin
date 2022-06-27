<?php

namespace Osm\Admin\Ui\MenuItem;

use Osm\Admin\Samples\Products\Product;
use Osm\Admin\Schema\Table as SchemaTable;
use Osm\Admin\Ui\MenuItem;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use function Osm\__;
use function Osm\ui_query;

/**
 * @property string $table_name #[Serialized]
 * @property SchemaTable $table
 *
 * @uses Serialized
 */
class Table extends MenuItem
{
    protected function get_table_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table(): SchemaTable {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->tables[$this->table_name];
    }

    protected function get_title(): string
    {
        return __($this->table->s_objects);
    }

    protected function get_url(): string
    {
        return ui_query($this->table_name)->toUrl('GET /');
    }
}