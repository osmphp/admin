<?php

namespace Osm\Admin\Ui\View\List_;

use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\View\List_;
use function Osm\__;

/**
 * @property \Osm\Admin\Ui\List_\Grid $model
 * @property array $data
 */
class Grid extends List_
{
    public string $template = 'ui::grid';

    protected function get_query(): Query
    {
        $query = parent::get_query();

        $query->query->select(...$this->model->selects);

        return $query;
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
                'edit_url' => $this->table->url('GET /edit'),
                'delete_url' => $this->table->url('DELETE /'),
                's_deleting' => __($this->table->s_deleting_n_objects),
                's_deleted' => __($this->table->s_n_objects_deleted),
            ],
        ];
    }
}