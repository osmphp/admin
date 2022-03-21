<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Blade\View;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view;
use function Osm\view_response;

/**
 * @property List_ $list_view
 */
#[Ui(Admin::class), Name('GET /')]
class ListPage extends Route
{
    protected function get_list_view(): List_|View {
        return view($this->table->list_views['grid'], [
            'http_query' => $this->http->query,
        ]);
    }

    public function run(): Response {
        return view_response($this->list_view->template, $this->list_view->data,
            status: $this->list_view->result->count > 0 ? 200 : 404);
    }
}