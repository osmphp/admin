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
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property string $title #[Serialized]
 * @property string $section_name #[Serialized]
 * @property Field[] $fields #[Serialized]
 */
class Group extends Object_
{
    protected function get_form(): Form {
        throw new Required(__METHOD__);
    }

    protected function get_section(): Section {
        return $this->form->sections[$this->section_name];
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

    protected function get_section_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_chapter(): Chapter {
        return $this->section->chapter;
    }

    protected function get_fields(): array {
        return array_filter($this->form->fields, fn(Field $field) =>
            $field->group_name === $this->name);
    }
}