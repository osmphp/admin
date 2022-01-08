<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Forms\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

#[Interface_(Admin::class), Name('GET /edit')]
class EditPage extends Route
{
    public function run(): Response
    {
        return view_response($this->form->template, [
            'form' => $this->form,
            'route_name' => $this->route_name,
            'object_count' => $this->object_count,
            'title' => $this->object_count === 1
                ? $this->object->title ?? "#{$this->object->id}"
                : __($this->interface->s_n_objects, [
                    'count' => $this->object_count,
                ]),
            'object' => $this->object,
        ]);
    }
}