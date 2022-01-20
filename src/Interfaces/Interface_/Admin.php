<?php

namespace Osm\Admin\Interfaces\Interface_;

use Osm\Admin\Interfaces\Interface_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string $s_object #[Serialized]
 * @property string $s_objects #[Serialized]
 * @property string $s_object_s #[Serialized]
 * @property string $s_new_object #[Serialized]
 * @property string $s_saving_new_object #[Serialized]
 * @property string $s_new_object_saved #[Serialized]
 * @property string $s_n_objects #[Serialized]
 * @property string $s_object_id #[Serialized]
 * @property string $s_n_m_objects_selected #[Serialized]
 * @property string $s_deleting_n_objects #[Serialized]
 * @property string $s_n_objects_deleted #[Serialized]
 */
class Admin extends Interface_
{
    protected function get_s_object(): string {
        throw new Required(__METHOD__);
    }

    protected function get_s_objects(): string {
        return "{$this->s_object}s";
    }

    protected function get_s_object_s(): string {
        return "{$this->s_object}(s)";
    }

    protected function get_s_new_object(): string {
        return "New {$this->s_object}";
    }

    protected function get_s_saving_new_object(): string {
        $object = mb_strtolower($this->s_object);
        return "Saving new {$object} ...";
    }

    protected function get_s_new_object_saved(): string {
        $object = mb_strtolower($this->s_object);
        return "New {$object} saved successfully.";
    }

    protected function get_s_n_objects(): string {
        return ":count {$this->s_objects}";
    }

    protected function get_s_object_id(): string {
        return "{$this->s_object} #:id";
    }

    protected function get_s_n_m_objects_selected(): string {
        $object_s = mb_strtolower($this->s_object_s);

        return ":selected / :count {$object_s} selected";
    }

    protected function get_s_deleting_n_objects(): string {
        $object_s = mb_strtolower($this->s_object_s);

        return "Deleting :selected {$object_s} ...";
    }

    protected function get_s_n_objects_deleted(): string {
        $object_s = mb_strtolower($this->s_object_s);

        return ":selected {$object_s} deleted.";
    }
}