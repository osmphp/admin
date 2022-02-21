<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Property;
use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property Control $control #[Serialized]
 * @property string[] $before #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string $in #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_control(): Control {
        return Control\Input::new();
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

    protected function around___wakeup(callable $proceed) {
        $this->control->property = $this;
    }

    protected function around_parse(callable $proceed): void {
        $proceed();

        $this->control->property = $this;
    }
}