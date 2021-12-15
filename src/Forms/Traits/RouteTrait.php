<?php

namespace Osm\Admin\Forms\Traits;

use Osm\Admin\Forms\Form;
use Osm\Admin\Interfaces\Route;
use Osm\Core\Attributes\UseIn;

/**
 * @property Form $form
 */
#[UseIn(Route::class)]
trait RouteTrait
{
    protected function get_form(): Form {
        /* @var Route|static $this */
        return $this->interface->form;
    }
}