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

#[Ui(Admin::class), Name('POST /')]
class Edit extends Route
{
    public function run(): Response
    {
        $item = json_decode($this->http->content, flags: JSON_THROW_ON_ERROR);
        if (!is_object($item)) {
            return plain_response(__("Object expected"), 500);
        }

        $query = ui_query($this->table->name);

        $query
            ->fromUrl($this->http->query,
                'limit', 'offset', 'order', 'select')
            ->update($item);

        return json_response((object)[]);
    }
}