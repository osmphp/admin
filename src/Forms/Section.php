<?php

namespace Osm\Admin\Forms;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Chapter $chapter
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property Fieldset[] $fieldsets #[Serialized]
 */
class Section extends Object_
{
    public string $template = 'forms::section';

    protected function get_chapter(): Chapter {
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

    protected function get_fieldsets(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            $fieldset->section = $this;
        }
    }
}