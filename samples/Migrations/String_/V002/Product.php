<?php

namespace Osm\Admin\Samples\Migrations\String_\V002;

use Osm\Admin\Schema\Attributes\Fixture;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Attributes\Explicit;

/**
 * @property ?string $description #[Explicit]
 *
 * @uses Explicit
 */
#[Fixture]
class Product extends Record
{

}