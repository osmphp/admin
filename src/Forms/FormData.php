<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Storages\Storage;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Traits\SubTypes;
use function Osm\__;

/**
 * @property Form $def
 * @property array $http_query
 * @property array $options
 * @property Interface_ $interface
 * @property \stdClass $object
 * @property int $count
 * @property Class_ $class
 * @property Storage $storage
 */
class FormData extends Object_
{
    use SubTypes;

    protected function get_def(): Form {
        throw new Required(__METHOD__);
    }

    protected function get_http_query(): array {
        throw new Required(__METHOD__);
    }

    protected function get_options(): array {
        return [
            's_saving_new_object' => __($this->interface->s_saving_new_object),
            's_new_object_saved' => __($this->interface->s_new_object_saved),
        ];
    }

    protected function get_interface(): Interface_ {
        return $this->def->interface;
    }

    protected function get_class(): Class_ {
        return $this->interface->class;
    }

    protected function get_storage(): Storage {
        return $this->class->storage;
    }

    protected function get_object(): \stdClass {
        throw new NotImplemented($this);
    }

    protected function get_count(): int {
        throw new NotImplemented($this);
    }

    protected function parseFilters(): void {
        throw new NotImplemented($this);
    }
}