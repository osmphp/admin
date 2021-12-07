<?php

namespace Osm\Admin\Forms\Routes\Admin;

use Osm\Admin\Forms\Routes\Route;
use Osm\Core\Exceptions\NotImplemented;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\plain_response;
use function Osm\query;

class Save extends Route
{
    public function run(): Response
    {
        $item = json_decode($this->http->content, flags: JSON_THROW_ON_ERROR);
        if (!is_object($item)) {
            return plain_response('Object expected', 500);
        }

        $id = query($this->class_name)->insert($item);

        throw new NotImplemented($this);
        //throw new \Exception('Error');
    }
}