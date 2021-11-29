<?php

namespace Osm\Admin\Formulas\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;

/**
 * @property Formula $expr #[Serialized]
 * @property bool $desc #[Serialized]
 */
#[Type('order')]
class Order extends Formula
{

}