<?php

namespace Osm\Admin\Icons;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $url #[Serialized]
 * @property string $title #[Serialized]
 * @property string $area_class_name #[Serialized]
 * @property string $template #[Serialized]
 */
class Icon extends Object_
{
    protected function get_url(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_area_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_template(): string {
        return 'icons::icon';
    }
}