<?php

namespace Osm\Admin\Base\Attributes\Table;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Attributes\Markers\Table\Column;
use Osm\Admin\Schema\Property;

#[\Attribute(\Attribute::TARGET_PROPERTY), Column('int')]
final class Int_
{
    public function __construct(
        public bool $unsigned = false,
        public ?string $references = null,
        public ?string $on_delete = null,
    )
    {
    }
}