<?php

namespace Osm\Admin\Ui;

use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property string $template
 * @property array $http_query
 *
 * @uses Serialized
 */
class View extends Object_
{
    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }

    protected function get_http_query(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->http->query;
    }
}