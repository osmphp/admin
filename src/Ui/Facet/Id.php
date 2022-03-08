<?php

namespace Osm\Admin\Ui\Facet;

use Osm\Admin\Ui\Facet;
use Osm\Core\Attributes\Type;

#[Type('id')]
class Id extends Facet
{
    public ?string $template = null;
}