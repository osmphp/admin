<?php

namespace Osm\Admin\Base\Attributes\Table;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\Markers\Table\Column;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('string')]
final class String_
{
    public function __construct()
    {
    }
}