<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\__;

/**
 * @property Struct $struct
 * @property string $name #[Serialized]
 * @property string $template #[Serialized]
 *
 * Render-time properties:
 *
 * @property Table $table
 * @property array $http_query
 * @property array $data
 * @property Query $query
 *
 * @uses Serialized
 */
class View extends Object_
{
    protected function get_struct(): Struct {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table(): Table {
        return $this->struct instanceof Table
            ? $this->struct
            : throw new NotSupported(__(":struct is not a table",
                ['struct' => $this->struct]));
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }

    protected function get_http_query(): array {
        throw new Required(__METHOD__);
    }

    protected function get_data(): array {
        throw new Required(__METHOD__);
    }

    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }
}