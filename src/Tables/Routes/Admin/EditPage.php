<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Forms\Form;
use Osm\Admin\Interfaces\Data;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property Data $data
 */
#[Interface_(Admin::class), Name('GET /edit')]
class EditPage extends Route
{
    public function run(): Response
    {
        return view_response($this->form->template, [
            'form' => $this->form,
            'data' => $this->data,
        ]);
    }

    protected function get_data(): Data {
        return Data\Edit::new([
            'http_query' => $this->http->query,
        ]);
    }
}