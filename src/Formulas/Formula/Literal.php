<?php

namespace Osm\Admin\Formulas\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 * @property mixed $value #[Serialized]
 */
#[Type('literal')]
class Literal extends Formula
{

}