<?php

namespace Osm\Admin\Schema;

use Osm\Core\Attributes\Type;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Object_;

/**
 *
 * @uses Serialized
 */
#[Type('class')]
class Class_ extends Struct
{
    public const SCHEMA_PROPERTY = 'classes';
    public const ROOT_CLASS_NAME = Object_::class;

}