<?php

namespace Osm\Data\Base\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Data\Base\Attributes\Type as TypeAttribute;

/**
 * @property ?string $type #[Serialized]
 */
trait Type
{
    protected function get_type(): ?string {
        /* @var Object_ $this */
        /* @var TypeAttribute $type */

        return ($type = $this->__class->attributes[TypeAttribute::class] ?? null)
            ? $type->name
            : null;
    }
}