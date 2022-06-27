<?php

namespace Osm\Admin\Ui;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property string $title
 * @property string $url
 */
class MenuItem extends Object_
{
    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        throw new Required(__METHOD__);
    }
}