<?php

namespace Osm\Admin\Samples\Products;

use Osm\Admin\Base\Attributes\Grid;
use Osm\Admin\Base\Attributes\Icon;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Storage;
use Osm\Admin\Base\Traits\Id;
use Osm\Admin\Base\Traits\SubTypes;
use Osm\Admin\Base\Attributes\Interface_;
use Osm\Admin\Base\Attributes\Form;
use Osm\Admin\Base\Attributes\Table;

/**
 * @property string $sku #[
 *      Serialized,
 *      Table\String_,
 *      Grid\String_('SKU', edit_link: true),
 *      Form\String_(10, 'SKU'),
 * ]
 * @property string $title #[
 *      Serialized,
 *      Table\String_,
 *      Grid\String_('Title', edit_link: true),
 *      Form\String_(20, 'Title'),
 * ]
 * @property ?string $description #[
 *      Serialized,
 *      Table\String_,
 *      Grid\String_('Description'),
 *      Form\String_(30, 'Description'),
 * ]
 */
#[
    Storage\Table('products'),
    Interface_\Table\Admin('/products', 'Product'),
    Grid(['sku', 'title']),
]
class Product extends Object_
{
    use Id, SubTypes;
}