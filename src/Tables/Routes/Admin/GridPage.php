<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\view_response;

/**
 * @property string $create_url
 * @property string $edit_url
 */
#[Interface_(Admin::class), Name('GET /')]
class GridPage extends Route
{
    public bool $can_show_all = true;

    public function run(): Response {
        return view_response($this->grid->template, [
            'grid' => $this->grid,
            'interface' => $this->interface,
            'route_name' => $this->route_name,
            'object_count' => $this->object_count,
            'title' => __($this->interface->s_objects),
            'objects' => $this->objects,
            'options' => $this->options,
            'create_url' => $this->create_url,
            'editUrl' => fn ($object) => $this->editUrl($object),
        ]);
    }

    protected function get_create_url(): string {
        return $this->interface->url('GET /create');
    }

    protected function get_edit_url(): string {
        return $this->interface->url('GET /edit');
    }

    protected function get_columns(): array {
        return array_unique(array_merge(['id'], $this->grid->select));
    }

    protected function editUrl(\stdClass $object): string {
        return "{$this->edit_url}?id={$object->id}";
    }

    protected function get_options(): array {
        return [
            's_selected' => __($this->interface->s_n_m_objects_selected),
            'count' => $this->object_count,
            'edit_url' => $this->edit_url,
            'delete_url' => $this->delete_url,
            's_deleting' => __($this->interface->s_deleting_n_objects),
            's_deleted' => __($this->interface->s_n_objects_deleted),
        ];
    }
}