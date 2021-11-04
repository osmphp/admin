<?php

namespace Osm\Admin\Grids\Grid;

use Osm\Admin\Grids\Grid;
use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property string $title #[Serialized]
 * @property bool $can_create #[Serialized]
 * @property string $create_url
 */
#[Name('page')]
class Page extends Grid
{
    protected function get_create_url(): string {
        global $osm_app; /* @var App $osm_app */

        if (!$this->can_create) {
            throw new Required(__METHOD__);
        }

        return "{$osm_app->area_url}{$this->url}/create";
    }
}