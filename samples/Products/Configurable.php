<?php

namespace Osm\Admin\Samples\Products;

use Osm\Core\Attributes\Type;

/**
 * @property string[] $config_properties
 * @property Product[] $config_products
 */
#[Type('configurable')]
class Configurable extends Product
{

}