<?php

namespace Osm {

    use Osm\Admin\Queries\Query;
    use Osm\Admin\Ui\Query as UiQuery;
    use Osm\Core\App;
    use Osm\Core\Exceptions\NotImplemented;

    function query(string $className, array $data = []): Query {
        global $osm_app; /* @var App $osm_app */

        return Query::new(['table' => $osm_app->schema->tables[$className]]);
    }

    function ui_query(string $className): UiQuery
    {
        global $osm_app; /* @var App $osm_app */

        return UiQuery::new(['table' => $osm_app->schema->tables[$className]]);
    }
}