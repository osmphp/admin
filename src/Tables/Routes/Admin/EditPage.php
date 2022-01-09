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

/**
 * @property string $title
 */
#[Interface_(Admin::class), Name('GET /edit')]
class EditPage extends Route
{
    public function run(): Response
    {
        return view_response($this->form->template, [
            'form' => $this->form,
            'route_name' => $this->route_name,
            'object_count' => $this->object_count,
            'title' => $this->title,
            'object' => $this->object,
            'form_url' => $this->form_url,
            'options' => $this->options,
            'field_options' => $this->field_options,
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

        $mergedObject = new \stdClass();

        if (!$this->objects) {
            return $mergedObject;
        }

        foreach ($this->columns as $column) {
            foreach ($this->objects as $key => $object) {
                if ($key === 0) {
                    $mergedObject->{$column} = $object->{$column};
                    continue;
                }

                if ($mergedObject->{$column} !== $object->{$column}) {
                    unset($mergedObject->{$column});
                    break;
                }
            }
        }

        return $mergedObject;
    }

    protected function get_form_url(): string {
        $url = $this->interface->url('POST /');

        return $this->interface->filterUrl($url, $this->applied_filters);
    }

    protected function get_options(): array {
        return array_merge(parent::get_options(), [
            's_saving' => __("Saving :title ...", ['title' => $this->title]),
            's_saved' => __(":title saved successfully.", ['title' => $this->title]),
        ]);
    }

    protected function get_title(): string {
        return $this->object_count === 1
            ? $this->object->title ?? __($this->interface->s_object_id, [
                'id' => $this->object->id,
            ])
            : __($this->interface->s_n_objects, [
                'count' => $this->object_count,
            ]);
    }
}