<?php

namespace Osm\Admin\Tables\Traits\Formula;

use Osm\Admin\Formulas\Formula;
use Osm\Admin\Tables\Traits\FormulaTrait;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(Formula\Literal::class)]
trait LiteralTrait
{
    use FormulaTrait;

    public function tables_value(): mixed {
        /* @var Formula\Literal|static $this */
        return $this->value;
    }
}