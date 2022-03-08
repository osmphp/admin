<?php

namespace Osm\Admin\Ui\Filter;

use Osm\Admin\Ui\Filter;
use Osm\Core\Attributes\Type;

#[Type('id')]
class Id extends Filter
{
    public ?string $template = null;
}