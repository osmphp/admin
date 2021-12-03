<?php

namespace Osm\Admin\Tables\IndexingSources;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Source;
use Osm\Core\Attributes\Type;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $type_name #[Serialized]
 */
#[Type('to')]
class To extends Source
{
    public bool $notify_inserted = true;
    public bool $notify_updated = true;

    public function create(): void {
    }

    protected function handle(int $id): void {
        $this->indexer->index(id: $id);
    }
}