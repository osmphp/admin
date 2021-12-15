<?php

namespace Osm\Admin\Forms\Field;

use Osm\Admin\Forms\Field;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\Type;

#[Type('int')]
class Int_ extends Field
{
    public string $template = 'forms::field.int';
}