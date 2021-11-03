<?php

namespace Osm\Admin\Migrations\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Admin\Migrations\Migrator;
use Osm\Admin\Schema\Schema;

#[UseIn(Schema::class)]
trait SchemaTrait
{
    public function migrate() {
        Migrator::new(['new' => $this])->migrate();
    }
}