<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Filterable;
use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\FormMode;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Http\Exceptions\NotFound;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

/**
 * @property Form $form_view
 */
#[Ui(Admin::class), Name('GET /edit')]
class EditPage extends Route
{
    protected function get_form_view(): Form {
        $form = clone $this->table->form_view;

        $form->http_query = $this->http->query;

        return $form;
    }

    public function run(): Response
    {
        if ($this->form_view->count === 0) {
            throw new NotFound();
        }

        return view_response($this->form_view->template, $this->form_view->data);
    }

    protected function get_data(): array {
        return [
            'title' => $this->title,
            'form_url' => $this->table->url('POST /'),
            'mode' => FormMode::Edit,
            'query' => $this->query,
            'js' => [
                'mode' => FormMode::Edit->value,
                's_saving' => __("Saving :title ...", ['title' => $this->title]),
                's_saved' => __(":title saved successfully.", ['title' => $this->title]),
                'delete_url' => $this->table->url('DELETE /'),
                's_deleting' => __("Deleting :title ...", ['title' => $this->title]),
                's_deleted' => __(":title deleted.", ['title' => $this->title]),
            ],
        ];
    }

    protected function get_title(): string {
        return match ($this->query->count) {
            1 => $this->query->items[0]->title,
            2 => __(":first and :second", [
                'first' => $this->query->items[0]->title,
                'second' => $this->query->items[1]->title,
            ]),
            default => __(":first, :second and :count other :items", [
                'first' => $this->query->items[0]->title,
                'second' => $this->query->items[1]->title,
                'count' => $this->query->count - 2,
                'items' => __($this->table->s_object_s),
            ]),
        };
    }

    protected function get_query(): Query
    {
        $query = parent::get_query()
            ->count();

        $query->query->select('id');

        foreach ($this->table->grid->selects as $column) {
            $query->query->select($column->formula
                ? "{$column->formula} AS {$column->name}"
                : $column->name);
        }

        return $query;
    }
}