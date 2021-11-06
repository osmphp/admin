<?php

namespace Osm\Admin\Forms\Section;

use Osm\Admin\Forms\Section;
use Osm\Core\Attributes\Name;

#[Name('standard')]
class Standard extends Section
{
    public string $template = 'forms::section';

}