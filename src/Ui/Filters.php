<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Struct;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View as BaseView;
use function Osm\theme_specific;

/**
 * Render-time properties:
 *
 * @property Query $query
 * @property Filter[] $filters
 * @property Struct $struct
 * @property bool $visible
 */
class Filters extends BaseView
{
    public string $template = 'ui::filters';

    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }

    protected function get_struct(): Struct {
        throw new Required(__METHOD__);
    }

    protected function get_filters(): array {
        $filters = [];

        foreach ($this->struct->properties as $property) {
            if (!$property->filterable) {
                continue;
            }

            if (!$property->filter) {
                continue;
            }

            $filters[] = theme_specific($property->filter, [
                'query' => $this->query,
            ]);
        }

        return $filters;
    }

    protected function get_visible(): bool {
        foreach ($this->filters as $filter) {
            if ($filter->visible) {
                return true;
            }
        }

        return false;
    }

    protected function get_data(): array {
        return [
            'filters' => $this->filters,
        ];
    }

    public function prepare(): void {
        foreach ($this->filters as $filter) {
            $filter->prepare();
        }
    }
}