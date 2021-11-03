<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Admin\Schema\Class_;
use Osm\Admin\Base\Attributes\Table;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $table #[Serialized]
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_table(): ?string {
        /* @var Class_|static $this */
        /* @var Table $table */
        return ($table = $this->reflection->attributes[Table::class] ?? null)
            ? $table->name
            : null;
    }
}