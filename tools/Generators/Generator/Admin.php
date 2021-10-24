<?php

namespace Osm\Data\Tools\Generators\Generator;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Tools\Generators\Generator;

class Admin extends Generator
{
    public function run(): void {
        foreach ($this->schema->tables as $table) {
            throw new NotImplemented($this);
        }
    }
}