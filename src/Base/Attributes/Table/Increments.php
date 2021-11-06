<?php

namespace Osm\Admin\Base\Attributes\Table;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\Markers\Table\Column;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('increments')]
final class Increments
{
}