<?php

namespace Osm {

    use Osm\Admin\Api\Api;
    use Osm\Admin\Queries\Query;
    use Osm\Core\Exceptions\NotImplemented;

    function query(string $className): Query {
        throw new NotImplemented();
    }

    function api(string $className): Api {
        throw new NotImplemented();
    }
}