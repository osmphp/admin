<?php

namespace Osm\Admin\Interfaces\Traits;

use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $control_type #[Serialized]
 * @property string[] $before #[Serialized]
 * @property string[] $after #[Serialized]
 * @property string $in #[Serialized]
 */
#[UseIn(Property::class)]
trait PropertyTrait
{
    protected function get_control_type(): string {
        return 'input';
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