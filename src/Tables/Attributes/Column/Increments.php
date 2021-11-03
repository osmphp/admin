<?php

namespace Osm\Admin\Tables\Attributes\Column;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Schema\Property;
use Osm\Admin\Tables\Attributes\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Increments extends Column
{
}