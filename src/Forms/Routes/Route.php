<?php

namespace Osm\Admin\Forms\Routes;

use Osm\Admin\Forms\Form;
use Osm\Core\App;
use Osm\Framework\Http\Route as BaseRoute;

/**
 * @property string $data_class_name
 * @property string $form_name
 * @property Form $form
 */
class Route extends BaseRoute
{
    protected function get_form(): Form {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema
            ->classes[$this->data_class_name]
            ->forms[$this->form_name];
    }
}