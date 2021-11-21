<?php

namespace Osm\Admin\Indexing\Traits;

use Osm\Admin\Base\Attributes\Indexer\Source;
use Osm\Admin\Base\Attributes\Indexer\Target;
use Osm\Admin\Indexing\Index;
use Osm\Admin\Indexing\Indexer;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property Index[] $indexes #[Serialized]
 */
#[UseIn(Schema::class)]
trait SchemaTrait
{
    protected function get_indexes(): array {
        global $osm_app; /* @var App $osm_app */

        $classes = $osm_app->descendants->classes(Indexer::class);
        $indexes = [];

        foreach ($classes as $class) {
            if (!isset($class->attributes[Target::class])) {
                continue;
            }

            if (empty($class->attributes[Source::class])) {
                continue;
            }

            $indexes[$class->name] = Index::new([
                'schema' => $this,
                'name' => $class->name,
                'reflection' => $class,
            ]);
        }

        return $indexes;
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->indexes as $class) {
            $class->schema = $this;
        }
    }
}