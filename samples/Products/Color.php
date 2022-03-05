<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Schema\Attributes\Title;
use Osm\Admin\Schema\Option;

class Color extends Option
{
    #[Title('Pinky')]
    const PINK = 'pink';
    const BLUE = 'blue';
    const WHITE = 'white';
    const BLACK = 'black';
}