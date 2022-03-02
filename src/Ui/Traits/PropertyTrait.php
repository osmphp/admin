<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Property;
use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Control $control #[Serialized]
 * @property string[] $before #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string $in #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_control(): ?Control {
        /* @var Property|static $this */
        return $this->data_type->control
            ? clone $this->data_type->control
            : null;
    }

    protected function get_before(): array {
        return [];
    }

    protected function get_after(): array {
        return [];
    }

    protected function get_in(): string {
        return '///';
    }
}