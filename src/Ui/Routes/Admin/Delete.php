<?php

namespace Osm\Admin\Ui\Routes\Admin;

use Osm\Admin\Ui\Attributes\Ui;
use Osm\Admin\Ui\Hints\UrlAction;
use Osm\Admin\Ui\Routes\Route;
use Osm\Core\Attributes\Name;
use Osm\Framework\Areas\Admin;
use Symfony\Component\HttpFoundation\Response;
use function Osm\__;
use function Osm\json_response;
use function Osm\plain_response;
use function Osm\ui_query;

#[Ui(Admin::class), Name('DELETE /')]
class Delete extends Route
{
    public function run(): Response
    {
        $query = ui_query($this->table->name);

        $query
            ->fromUrl($this->http->query,
                'limit', 'offset', 'order', 'select')
            ->delete();

        return json_response((object)[
            'url' => $this->table->url('GET /'),
        ]);
    }
}