<?php

namespace Osm\Admin\Ui\Exceptions;

use Osm\Core\App;
use Osm\Framework\Http\Exceptions\Http;
use Symfony\Component\HttpFoundation\Response;

class UnsafeOperation extends Http
{
    public function response(): Response
    {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->http->responses->forbidden($this->getMessage());
    }
}