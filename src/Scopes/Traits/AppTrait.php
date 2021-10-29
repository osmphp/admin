<?php

namespace Osm\Data\Scopes\Traits;

use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Data\Scopes\Scope;

/**
 * @property Scope $root_scope
 * @property Scope $scope
 */
#[UseIn(App::class)]
trait AppTrait
{
    protected function get_root_scope(): Scope {
        return Scope::new();
    }
}