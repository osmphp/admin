<?php

namespace Osm\Admin\Ui\List_;

use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\View;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use function Osm\__;
use Osm\Admin\Queries\Formula;

/**
 * @property string[] $selects #[Serialized]
 *
 * Render-time properties:
 *
 * @property Control[] $columns
 *
 * @uses Serialized
 */
class Grid extends List_
{
    public string $template = 'ui::grid';

    protected function get_query(): Query
    {
        $query = parent::get_query();

        $query->query->select(...$this->selects);

        return $query;
    }

    protected function get_selects(): array {
        return ['title'];
    }

    protected function get_data(): array {
        return [
            'grid' => $this,
            'table' => $this->table,
            'query' => $this->query,
            'title' => $this->table->s_objects,
            'create_url' => $this->table->url('GET /create'),
            'js' => [
                's_selected' => __($this->table->s_n_m_objects_selected),
                'count' => $this->query->count,
                'edit_url' => $this->edit_url,
                'delete_url' => $this->table->url('DELETE /'),
                's_deleting' => __($this->table->s_deleting_n_objects),
                's_deleted' => __($this->table->s_n_objects_deleted),
            ],
        ];
    }

    protected function get_columns(): array {
        $columns = [];

        foreach ($this->query->query->selects as $select) {
            $control = $select->expr instanceof Formula\Identifier
                ? $select->expr->property->control
                : $select->data_type->default_control;

            if (!$control) {
                continue;
            }

            $columns[$select->alias] = $control = clone $control;
            $control->name = $select->alias;
            $control->view = $this;
        }

        return $columns;
    }
}