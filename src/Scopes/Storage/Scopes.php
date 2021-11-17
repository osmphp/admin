<?php

namespace Osm\Admin\Scopes\Storage;

use Osm\Admin\Scopes\Scopes as ScopeQuery;
use Osm\Admin\Storages\Storage;
use Osm\Admin\Tables\Table;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;

/**
 * @method ScopeQuery query()
 */
#[Type('scopes')]
class Scopes extends Table
{
    public function seed(?Storage $current): void
    {
        $this->query()->insert((object)[
            'title' => __('Global'),
        ]);
    }
}