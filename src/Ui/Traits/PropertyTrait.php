<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Property;
use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\Facet;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property string[] $before #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string $in #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_before(): array {
        return [];
    }

    protected function get_after(): array {
        return [];
    }

    protected function get_in(): string {
        return '///';
    }

    protected function around___wakeup(callable $proceed): void {
        /* @var Property|static $this */

        $proceed();

        if ($this->control) {
            $this->control->property = $this;
            $this->control->data_type = $this->data_type;
        }

        if ($this->facet) {
            $this->facet->property = $this;
        }
    }

}