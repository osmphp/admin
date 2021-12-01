<?php

namespace Osm\Admin\Storages;

use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Indexing\Index;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Schema\Property;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\__;

/**
 * @property Class_ $class
 * @property string $name #[Serialized]
 * @property int $version #[Serialized]
 * @property ?string $query_class_name #[Serialized]
 * @property string[] $source_to_class_names #[Serialized]
 * @property Index[] $source_to
 * @property string[] $targeted_by_class_names #[Serialized]
 * @property Index[] $targeted_by
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

    protected function get_source_to_class_names(): array {
        $classNames = [];

        foreach ($this->class->schema->indexes as $index) {
            if (in_array($this->name, $index->sources)) {
                $classNames[] = $index->name;
            }
        }

        return $classNames;
    }

    protected function get_source_to(): array {
        return array_map(
            fn(string $indexClassName)
            => $this->class->schema->indexes[$indexClassName],
            $this->source_to_class_names);
    }

    protected function get_targeted_by_class_names(): array {
        $classNames = [];

        foreach ($this->class->schema->indexes as $index) {
            if ($index->target !== $this->name) {
                continue;
            }

            if (isset($classNames[$index->target_type ?? ''])) {
                throw new NotSupported(__(
                    "':index1' and ':index2' indexers can't target the same type ':type' of data class ':class'", [
                        'index1' => $index->name,
                        'index2' => $classNames[$index->target_type ?? ''],
                        'class' => $this->class->name,
                        'type' => $index->target_type ?? '',
                    ]
                ));
            }

            $classNames[$index->target_type ?? ''] = $index->name;
        }

        return $classNames;
    }

    protected function get_targeted_by(): array {
        return array_map(
            fn(string $indexClassName)
            => $this->class->schema->indexes[$indexClassName],
            $this->targeted_by_class_names);
    }
}