<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

#[Interface_(Admin::class), Name('GET /create')]
class CreatePage extends Route
{
    public function run(): Response {
        return view_response($this->form->template, [
            'form' => $this->form,
            'route_name' => $this->route_name,
            'title' => __($this->interface->s_new_object),
            'object' => $this->object,
        ]);
    }

   protected function get_object(): \stdClass {
        return new \stdClass();
    }
}