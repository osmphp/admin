<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use function Osm\__;
use function Osm\ui_query;

/**
 * Render-time properties:
 *
 * @property int $count
 * @property \stdClass $item
 * @property string $title
 */
class Form extends View
{
    public string $template = 'ui::form';

    protected function get_query(): Query {
        $query = ui_query($this->table->name)
            ->all()
            ->url($this->http_query, 'select')
            ->count();

        $query->query->select('id', 'title');

        return $query;
    }

    protected function get_count(): int {
        return $this->query->count;
    }

    protected function get_item(): \stdClass {
        if ($this->count === 0) {
            return new \stdClass();
        }

        if ($this->count === 1) {
            return $this->query->items[0];
        }

        throw new NotImplemented($this);
    }

    protected function get_title(): string {
        if ($this->count === 0) {
            return __($this->struct->s_new_object);
        }

        if ($this->count === 1) {
            return $this->item->title;
        }

        throw new NotImplemented($this);
    }

    protected function get_data(): array {
        return [
            'title' => $this->title,
        ];
    }
}