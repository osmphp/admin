<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Forms\Form;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property \stdClass $data
 */
#[Interface_(Admin::class), Name('GET /create')]
class CreatePage extends Route
{
    public function run(): Response {
        return view_response($this->form->template, [
            'form' => $this->form,
            'mode' => Form::CREATE_MODE,
            'data' => $this->data,
        ]);
    }

    protected function get_data(): \stdClass {
        return (object)[];
    }
}