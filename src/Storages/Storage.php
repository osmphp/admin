<?php

namespace Osm\Admin\Storages;

use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Class_ $class
 * @property int $version #[Serialized]
 * @property ?string $query_class_name #[Serialized]
 */
class Storage extends Object_
{
    use SubTypes;

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    public function create(): void {
        // By default, a storage is read-only, and gets the data from
        // something that already exists. For example, it may be a PHP array.
        // As such, it doesn't need any preparation.
    }

    public function alter(Storage $current): void {
        // By default, a storage doesn't need any preparation. See `create()`.
    }

    public function drop(): void {
        // By default, a storage doesn't need any preparation. See `create()`.
    }

    public function seed(?Storage $current): void {
        // By default, a storage doesn't need any preparation. See `create()`.
    }

    public function query(): Query {
        if (!$this->query_class_name) {
            return $this->genericQuery();
        }

        $new = "{$this->query_class_name}::new";
        return $new(['storage' => $this]);
    }

    protected function genericQuery(): Query {
        throw new NotImplemented($this);
    }
}