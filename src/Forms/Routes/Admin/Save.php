<?php

namespace Osm\Admin\Forms\Routes\Admin;

use Osm\Admin\Forms\Routes\Route;
use Osm\Core\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

class Save extends Route
{
    public function run(): Response
    {
        throw new NotImplemented($this);
    }
}