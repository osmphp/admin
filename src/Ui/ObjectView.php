<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Blade\View;
use function Osm\__;
use Osm\Framework\Blade\Attributes\RenderTime;

/**
 * A view that displays one or more existing objects,
 * or provides mens for creating a new object.
 *
 * @property Struct $struct
 * @property string $name #[Serialized]
 * @property Table $table #[RenderTime]
 * @property array $http_query #[RenderTime]
 * @property Query $query #[RenderTime]
 * @property Result $result #[RenderTime]
 *
 * @uses Serialized, RenderTime
 */
class ObjectView extends View
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

    protected function get_http_query(): array {
        throw new Required(__METHOD__);
    }

    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }

    protected function get_result(): Result {
        return $this->query->result;
    }
}