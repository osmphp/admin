<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\DataType;
use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Control $control #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(DataType::class)]
trait DataTypeTrait
{
    protected function get_control(): ?Control {
        return Control\Input::new();
    }
}