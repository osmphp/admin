<?php

namespace Osm\Admin\Samples\Queries\V001;

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