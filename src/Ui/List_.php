<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\Required;
use function Osm\ui_query;

/**
 * Render-time properties:
 *
 * @property Table $table
 * @property Query $query
 * @property string $edit_url
 */
class List_ extends View
{
    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_query(): Query {
        $query = ui_query($this->table->name)
            ->all()
            ->url($this->http_query, 'id', 'select')
            ->count();

        $query->query->select('id');

        return $query;
    }

    protected function get_edit_url(): string {
        return $this->table->url('GET /edit');
    }
    public function editUrl(\stdClass $item): string {
        return "{$this->edit_url}/?id={$item->id}";
    }
}