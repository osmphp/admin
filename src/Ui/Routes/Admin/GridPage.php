<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

/**
 * @property array $data
 */
#[Ui(Admin::class), Name('GET /')]
class GridPage extends Route
{
    public function run(): Response
    {
        return view_response('ui::grid', $this->data);
    }

    protected function get_data(): array {
        return [
            'title' => $this->table->s_objects,
            'js' => [
                's_selected' => __($this->table->s_n_m_objects_selected),
                'count' => $this->query->count,
                'edit_url' => $this->table->url('GET /edit'),
                'delete_url' => $this->table->url('DELETE /'),
                's_deleting' => __($this->table->s_deleting_n_objects),
                's_deleted' => __($this->table->s_n_objects_deleted),
            ],
            'table' => $this->table,
            'query' => $this->query,
            'create_url' => $this->table->url('GET /create'),
            'grid' => $this->table->grid,
        ];
    }

    protected function get_query(): Query
    {
        $query = parent::get_query()
            ->count();

        $query->query->select('id');
        foreach ($this->table->grid->columns as $column) {
            $query->query->select($column->formula
                ? "{$column->formula} AS {$column->name}"
                : $column->name);
        }

        return $query;
    }
}