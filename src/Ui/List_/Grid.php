<?php

namespace Osm\Admin\Ui\List_;

use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\Facets;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Sidebar;
use Osm\Admin\Ui\View;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View as BaseView;
use function Osm\__;
use Osm\Admin\Queries\Formula;
use function Osm\theme_specific;

/**
 * @property string[] $selects #[Serialized]
 *
 * Render-time properties:
 *
 * @property Control[] $columns
 * @property Facets $facets
 *
 * @uses Serialized
 */
class Grid extends List_
{
    public string $template = 'ui::grid';

    protected function get_query(): Query
    {
        $this->configureQueryAndFilters();

        return $this->query;
    }

    protected function get_selects(): array {
        return ['title'];
    }

    protected function get_facets(): Facets|BaseView {
        $this->configureQueryAndFilters();

        return $this->facets;
    }

    protected function configureQueryAndFilters(): void {
        $this->query = parent::get_query();

        $this->query->db_query->select(...$this->selects);

        $this->facets = theme_specific(Facets::class, [
            'struct' => $this->struct,
            'query' => $this->query,
        ]);

        $this->facets->prepare();
    }

    protected function get_data(): array {
        return [
            'grid' => $this,
            'table' => $this->table,
            'query' => $this->query,
            'title' => $this->table->s_objects,
            'create_url' => $this->table->url('GET /create'),
            'sidebar' => theme_specific(Sidebar::class, [
                'facets' => $this->facets,
            ]),
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

        foreach ($this->query->db_query->selects as $select) {
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