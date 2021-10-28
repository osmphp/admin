<?php

namespace Osm\Data\Scopes\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Data\Schema\Class_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Scopes\Attributes\Scoped;

/**
 * @property bool $scoped #[Serialized]
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_scoped(): bool {
        /* @var Class_|static $this */

        return isset($this->reflection->attributes[Scoped::class]);;
    }
}