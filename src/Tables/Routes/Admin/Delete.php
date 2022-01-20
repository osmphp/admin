<?php

namespace Osm\Admin\Tables\Routes\Admin;

use Osm\Admin\Base\Attributes\Route\Interface_;
use Osm\Admin\Interfaces\Route;
use Osm\Admin\Tables\Interface_\Admin;
use Osm\Core\Attributes\Name;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\json_response;
use function Osm\plain_response;

#[Interface_(Admin::class), Name('DELETE /')]
class Delete extends Route
{
    public function run(): Response
    {
        $this->query->delete();

        return json_response((object)[
            'url' => $this->grid_url,
        ]);
    }
}