<?php

namespace Osm\Admin\Forms;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Fieldset $fieldset
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property string $template #[Serialized]
 */
class Field extends Object_
{
    use SubTypes;

    public const NEW_ = 'new';

    protected function get_group(): Fieldset {
        throw new Required(__METHOD__);
    }

    protected function get_sort_order(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }

    public function columns(array &$columns): void {
        $columns[$this->name] = true;
    }
}