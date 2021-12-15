<?php

namespace Osm\Admin\Interfaces\Interface_;

use Osm\Admin\Interfaces\Interface_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string $s_object #[Serialized]
 * @property string $s_objects #[Serialized]
 * @property string $s_new_object #[Serialized]
 * @property string $s_saving_new_object #[Serialized]
 * @property string $s_new_object_saved #[Serialized]
 */
class Admin extends Interface_
{
    protected function get_s_object(): string {
        throw new Required(__METHOD__);
    }

    protected function get_s_objects(): string {
        return "{$this->s_object}s";
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
}