<?php

namespace Osm\Admin\Forms\Field;

use Osm\Admin\Forms\Field;
use Osm\Core\Attributes\Name;

#[Name('int')]
class Int_ extends Field
{
    public string $template = 'forms::field.int';
}