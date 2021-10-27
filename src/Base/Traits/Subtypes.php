<?php

namespace Osm\Data\Base\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Data\Base\Attributes\Type;

/**
 * @property ?string $type #[Serialized]
 */
trait Subtypes
{
    protected function get_type(): ?string {
        /* @var Object_ $this */
        /* @var Type $type */

        return ($type = $this->__class->attributes[Type::class] ?? null)
            ? $type->name
            : null;
    }
}