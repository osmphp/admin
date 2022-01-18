<?php

namespace Osm\Admin\Grids;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Grid $grid
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property string $header_template
 */
class Column extends Object_
{
    use SubTypes;

    public string $template = 'grids::cell.column';

    protected function get_grid(): Grid {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_header_template(): string {
        throw new NotImplemented($this);
    }
}