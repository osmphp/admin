<?php

namespace Osm\Admin\Ui\Routes;

use Osm\Admin\Schema\Attributes\Safe;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Exceptions\UnsafeOperation;
use Osm\Admin\Ui\Query;
use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Http\Route as BaseRoute;
use function Osm\__;
use function Osm\ui_query;

/**
 * @property string $route_name
 * @property string $class_name
 * @property Table $table
 * @property bool $safe
 */
class Route extends BaseRoute
{
    protected function get_route_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_table(): Table {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->tables[$this->class_name];
    }

    protected function get_safe(): bool {
        return isset($this->__class->attributes[Safe::class]);
    }

    protected function assertSafe(Query $query): void {
        if ($this->safe) {
            return;
        }

        if (!empty($query->filters)) {
            return;
        }

        if (($this->http->query['all'] ?? null) === true) {
            return;
        }

        throw new UnsafeOperation(__(
            "Specify a filter in the URL query parameters, or confirm an operation on all objects using `?all` flag."));
    }
}