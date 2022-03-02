<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Ui\Control;
use Osm\Core\Attributes\UseIn;

/**
 * @property ?Control $control
 */
#[UseIn(Formula\SelectExpr::class)]
trait SelectExprFormulaTrait
{
    protected function get_control(): ?Control {
        /* @var Formula\SelectExpr|static $this */

        return $this->expr instanceof Formula\Identifier
            ? $this->expr->property->control
            : $this->data_type->control;
    }
}