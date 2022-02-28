<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

/**
 * @property List_ $list_view
 */
#[Ui(Admin::class), Name('GET /')]
class ListPage extends Route
{
    protected function get_list_view(): List_ {
        return $this->table->list_views['grid'];
    }

    public function run(): Response
    {
        $view = clone $this->list_view;

        return view_response($view->template, $view->data);
    }
}