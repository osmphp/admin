<?php

namespace Osm\Admin\Ui\View;

use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property \Osm\Admin\Ui\View $model
 * @property string $template
 * @property array $http_query
 */
class View extends Object_
{
    protected function get_model(): \Osm\Admin\Ui\View {
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