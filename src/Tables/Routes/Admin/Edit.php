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
use function Osm\query;

#[Interface_(Admin::class), Name('POST /')]
class Edit extends Route
{
    public function run(): Response
    {
        $item = json_decode($this->http->content, flags: JSON_THROW_ON_ERROR);
        if (!is_object($item)) {
            return plain_response(__("Object expected"), 500);
        }

        $this->query->update($item);

        return json_response((object)[]);
    }
}