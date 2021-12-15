<?php

namespace Osm\Admin\Forms;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Form $form
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property ?string $title #[Serialized]
 * @property Section[] $sections #[Serialized]
 */
class Chapter extends Object_
{
    public string $template = 'forms::chapter';

    protected function get_form(): string {
        throw new Required(__METHOD__);
    }

    protected function get_sort_order(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_sections(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->sections as $section) {
            $section->chapter = $this;
        }
    }
}