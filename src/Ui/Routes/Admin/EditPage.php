<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Blade\View;
use Osm\Framework\Http\Exceptions\NotFound;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view;
use function Osm\view_response;

/**
 * @property Form $form_view
 */
#[Ui(Admin::class), Name('GET /edit')]
class EditPage extends Route
{
    protected function get_form_view(): Form|View {
        return view($this->table->form_view, [
            'edit' => true,
            'http_query' => $this->http->query,
        ]);
    }

    public function run(): Response
    {
        if ($this->form_view->count === 0) {
            throw new NotFound();
        }

        return view_response($this->form_view->template, $this->form_view->data);
    }
}