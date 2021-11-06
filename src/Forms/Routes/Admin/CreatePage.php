<?php

namespace Osm\Admin\Forms\Routes\Admin;

use Osm\Admin\Forms\Routes\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

class CreatePage extends Route
{
    public function run(): Response
    {
        return view_response('forms::pages.create', [
            'form' => $this->form,
        ]);
    }
}