<?php

namespace Osm\Admin\Samples\Generics;

use Osm\Admin\Schema\Attributes\Table;
use Osm\Admin\Schema\Record;

/**
 * @property int $int
 * @property string $string
 */
#[Table('related_items')]
class Related extends Record
{

}