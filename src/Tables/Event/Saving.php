<?php

namespace Osm\Admin\Tables\Event;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Indexing\Event;
use Osm\Core\Attributes\Type;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $type_name #[Serialized]
 */
#[Type('saving')]
class Saving extends Event
{
    public bool $notify_inserted = true;
    public bool $notify_updated = true;
    public ?int $record_id;

    public function create(): void {
    }

    protected function handle(int $id): void {
        $this->record_id = $id;
        try {
            $this->indexer->index($this);
        }
        finally {
            $this->record_id = null;
        }
    }
}