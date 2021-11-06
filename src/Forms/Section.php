<?php

namespace Osm\Admin\Forms;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Form $form
 * @property Chapter $chapter
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property string $chapter_name #[Serialized]
 * @property Group[] $groups #[Serialized]
 * @property string $template #[Serialized]
 */
class Section extends Object_
{
    protected function get_form(): Form {
        throw new Required(__METHOD__);
    }

    protected function get_chapter(): Chapter {
        return $this->form->chapters[$this->chapter_name];
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

    protected function get_chapter_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_groups(): array {
        return array_filter($this->form->groups, fn(Group $group) =>
            $group->section_name === $this->name);
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }
}