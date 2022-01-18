<?php

namespace Osm\Admin\Base\Attributes\Grid;

use Osm\Admin\Base\Attributes\Markers\Grid\Column;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('int')]
final class Int_
{
    public function __construct(
        public string $title,
        public bool $edit_link = false,
    )
    {
    }
}