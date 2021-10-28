<?php

namespace Osm\Data\Migrations\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Data\Migrations\Migrator;
use Osm\Data\Schema\Schema;

#[UseIn(Schema::class)]
trait SchemaTrait
{
    public function migrate() {
        Migrator::new(['new' => $this])->migrate();
    }
}