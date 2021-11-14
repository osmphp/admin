<?php

namespace Osm\Admin\Migrations\Traits;

use Osm\Core\Attributes\UseIn;
use Osm\Admin\Migrations\Migrator;
use Osm\Admin\Schema\Schema;

trait SchemaTrait
{
    public function migrate() {
        Migrator::new(['new' => $this])->migrate();
    }
}