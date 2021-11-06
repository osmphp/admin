<?php

namespace Osm\Admin\Forms;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Form $form
 * @property Chapter $chapter
 * @property Section $section
 * @property Group $group
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property string $group_name #[Serialized]
 */
class Field extends Object_
{
    protected function get_form(): Form {
        throw new Required(__METHOD__);
    }

    protected function get_group(): Group {
        return $this->form->groups[$this->group_name];
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

    protected function get_group_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_chapter(): Chapter {
        return $this->group->chapter;
    }

    protected function get_section(): Section {
        return $this->group->section;
    }

}