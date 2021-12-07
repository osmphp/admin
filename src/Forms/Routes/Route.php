<?php

namespace Osm\Admin\Forms\Routes;

use Osm\Admin\Forms\Form;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Framework\Http\Route as BaseRoute;

/**
 * @property string $class_name
 * @property string $form_name
 * @property Class_ $class
 * @property Form $form
 */
class Route extends BaseRoute
{
    protected function get_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->class_name];
    }

    protected function get_form(): Form {
        return $this->class->forms[$this->form_name];
    }
}