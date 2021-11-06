<?php

namespace Osm\Admin\Forms\Routes\Admin;

use Osm\Admin\Forms\Form;
use Osm\Core\App;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property string $data_class_name
 * @property string $form_name
 * @property Form\Create $form
 */
class RenderCreateFormPage extends Route
{
    protected function get_form(): Form {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema
            ->classes[$this->data_class_name]
            ->forms[$this->form_name];
    }

    public function run(): Response
    {
        return view_response('forms::pages.create', [
            'form' => $this->form,
        ]);
    }
}