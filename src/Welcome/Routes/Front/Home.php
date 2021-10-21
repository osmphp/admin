<?php

declare(strict_types=1);

namespace My\Welcome\Routes\Front;

use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Areas\Front;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

#[Area(Front::class), Name('GET /')]
class Home extends Route
{
    public function run(): Response {
        return view_response('welcome::home');
    }
}