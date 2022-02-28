<?php

namespace Osm\Admin\Ui\List_;

use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\View;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string[] $selects #[Serialized]
 *
 * @uses Serialized
 */
class Grid extends List_
{
    public string $view_class_name = View\List_\Grid::class;

    protected function get_selects(): array {
        throw new Required(__METHOD__);
    }
}