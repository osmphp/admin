<?php

namespace Osm\Admin\Samples\Generics;

use Carbon\Carbon;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Explicit;
use Osm\Admin\Schema\Attributes\Length;
use Osm\Admin\Schema\Attributes\Virtual;
use Osm\Admin\Schema\Attributes\Computed;
use Osm\Admin\Schema\Attributes\Overridable;

/**
 * @property ?Item $parent #[Explicit]
 * @property ?string $type
 *
 * @property int $int
 * @property float $float
 * @property string $string
 * @property bool $bool
 * @property Carbon $datetime
 * @property mixed $mixed
 * @property Related $record #[Explicit]
 * @property Struct $object
 *
 * @property int[] $int_array
 *
 * @property ?string $nullable_string
 * @property ?Related $nullable_record #[Explicit]
 * @property ?Struct $nullable_object
 *
 * @property ?string $explicit_string #[Explicit, Length(255)]
 * @property ?string $explicit_text #[Explicit]
 * @property ?Struct $explicit_object #[Explicit]
 *
 * @property ?string $virtual #[Virtual("string")]
 * @property ?string $computed #[Computed("string")]
 * @property ?string $overridable #[Overridable("string")]
 *
 * @property ?string $explicit_computed #[Explicit, Length(255), Computed("string")]
 * @property ?string $explicit_overridable #[Explicit, Length(255), Overridable("string")]
 *
 * @uses Explicit, Length, Virtual, Computed, Overridable
 */
class Item extends Record
{

}