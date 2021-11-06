<?php

namespace Osm\Admin\Samples\Home\Routes\Admin;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

#[Area(Admin::class), Name('GET /')]
class HomePage extends Route
{
    public function run(): Response
    {
        return view_response('Osm_Admin_Samples_Home::pages.home');
    }
}