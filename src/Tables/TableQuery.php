<?php

namespace Osm\Admin\Tables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Indexing\Index;
use Osm\Admin\Tables\Traits\Insert;
use Osm\Admin\Tables\Traits\Select;
use Osm\Admin\Tables\Traits\Update;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Queries\Query;
use Osm\Admin\Queries\Result;
use Osm\Admin\Queries\Traits\Dehydrated;
use Osm\Admin\Base\Attributes\Table as TableAttribute;
use Osm\Framework\Db\Db;
use function Osm\hydrate;
use function Osm\merge;

/**
 * @property Table $storage
 * @property Db $db
 * @property string $name
 * @property QueryBuilder $raw
 */
class TableQuery extends Query
{
    use Select, Insert, Update;

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_name(): string {
        return $this->class->storage->name;
    }

    protected function get_raw(): QueryBuilder {
        return $this->db->table($this->name);
    }

    public function raw(callable $callback): static {
        $callback($this->raw);

        return $this;
    }
}