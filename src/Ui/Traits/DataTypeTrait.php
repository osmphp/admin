<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\DataType;
use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $supported_controls #[Serialized]
 * @property ?Control $default_control #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(DataType::class)]
trait DataTypeTrait
{
    protected function get_supported_controls(): array {
        return ['input'];
    }

    protected function get_default_control(): ?Control {
        return Control\Input::new();
    }
}