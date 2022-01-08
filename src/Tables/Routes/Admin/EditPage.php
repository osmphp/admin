<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
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

    protected function get_columns() :array {
        $columns = ['id' => true];
        if (isset($this->class->properties['title'])) {
            $columns['title'] = true;
        }

        // request columns for fields
        foreach ($this->form->fields() as $field) {
            $field->columns($columns);
        }

        return array_keys($columns);
    }

    protected function get_object(): \stdClass {
        if (!$this->object_count) {
            throw new NotSupported(__("Edit form should not be rendered for empty data set."));
        }

        if ($this->object_count === 1) {
            return $this->objects[0];
        }

        $object = new \stdClass();

        if (!$this->objects) {
            return $object;
        }

        // merge fetched objects into one
        throw new NotImplemented($this);
    }

}