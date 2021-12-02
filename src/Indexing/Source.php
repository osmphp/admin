<?php

namespace Osm\Admin\Indexing;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Indexer $indexer
 * @property string $name #[Serialized]
 * @property string $table #[Serialized]
 */
class Source extends Object_
{
    use SubTypes;

    public function createNotificationTable(Blueprint $table): void {
        throw new NotImplemented($this);
    }
}