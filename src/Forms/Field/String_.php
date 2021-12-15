<?php

namespace Osm\Admin\Forms\Field;

use Osm\Admin\Forms\Field;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\Type;

#[Type('string')]
class String_ extends Field
{
    public string $template = 'forms::field.string';
}