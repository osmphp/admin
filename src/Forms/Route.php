<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Interfaces\Route as BaseRoute;
use Osm\Core\Exceptions\NotImplemented;

class Route extends BaseRoute
{
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
}