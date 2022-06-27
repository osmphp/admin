<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Home;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Blade\View;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view;
use function Osm\view_response;

/**
 * @property Home $home
 */
#[Area(Admin::class), Name('GET /')]
class HomePage extends Route
{
    protected function get_home(): Home|View {
        return view(Home::class);
    }

    public function run(): Response
    {
        return view_response($this->home->template, $this->home->data);
    }
}