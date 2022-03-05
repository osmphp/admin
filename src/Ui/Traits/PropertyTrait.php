<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Property;
use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\Filter;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Control $control #[Serialized]
 * @property ?Filter $filter #[Serialized]
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
        return $this->data_type->default_control
            ? clone $this->data_type->default_control
            : null;
    }

    protected function get_filter(): ?Filter {
        /* @var Property|static $this */
        return $this->control->default_filter
            ? clone $this->control->default_filter
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