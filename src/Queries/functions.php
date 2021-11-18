<?php

namespace Osm {

    use Osm\Admin\Queries\Query;
    use Osm\Core\App;

    function query(string $className): Query {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$className]->storage->query();
    }

}

