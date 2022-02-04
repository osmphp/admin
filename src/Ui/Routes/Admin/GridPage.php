<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\plain_response;

#[Ui(Admin::class), Name('GET /')]
class GridPage extends Route
{
    public function run(): Response
    {
        return plain_response('Hello');
    }
}