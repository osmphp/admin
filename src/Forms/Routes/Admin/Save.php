<?php

namespace Osm\Admin\Forms\Routes\Admin;

use Osm\Admin\Forms\Routes\Route;
use Osm\Core\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;

class Save extends Route
{
    public function run(): Response
    {
        $item = json_decode($this->http->content, flags: JSON_THROW_ON_ERROR);
        throw new NotImplemented($this);
        //throw new \Exception('Error');
    }
}