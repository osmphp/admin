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
            'form_url' => $this->form_url,
            'options' => $this->options,
            'field_options' => $this->field_options,
        ]);
    }

   protected function get_object(): \stdClass {
        return new \stdClass();
    }

    protected function get_form_url(): string {
        return $this->interface->url('POST /create');
    }

    protected function get_options(): array {
        return array_merge(parent::get_options(), [
            's_saving' => $this->interface->s_saving_new_object,
            's_saved' => $this->interface->s_new_object_saved,
        ]);
    }
}