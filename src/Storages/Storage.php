<?php

namespace Osm\Admin\Storages;

use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Indexing\Indexer;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Indexing;
use function Osm\__;

/**
 * @property Class_ $class
 * @property string $name #[Serialized]
 * @property int $version #[Serialized]
 * @property ?string $query_class_name #[Serialized]
 * @property array $notifies_names #[Serialized]
 * @property Indexing\Source[] $notifies
 * @property Indexing\Module $indexing
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

    protected function get_indexing(): Indexing\Module|BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[Indexing\Module::class];
    }

    protected function get_notifies(): array {
        $notifies = [];

        foreach ($this->notifies_names as $indexerName => $sourceNames) {
            $indexer = $this->indexing->indexers[$indexerName];
            foreach ($sourceNames as $sourceName) {
                $notifies[] = $indexer->sources[$sourceName];
            }
        }

        return $notifies;
    }

    protected function get_notifies_names(): array {
        $names = [];

        foreach ($this->indexing->indexers as $indexer) {
            foreach ($indexer->sources as $source) {
                if ($this->name !== $source->table) {
                    continue;
                }

                if (!isset($names[$indexer->name])) {
                    $names[$indexer->name] = [];
                }

                $names[$indexer->name][] = $source->name;
            }
        }

        return $names;
    }
}