<?php

namespace Osm\Data\Tools\Generators\Generator;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Tools\Generators\Generator;
use Osm\Data\Tools\Generators\Project;
use Osm\Data\Tools\Generators\ProjectModule;
use Osm\Data\Tools\Generators\Table;

/**
 * @property Project $project
 */
class Admin extends Generator
{
    public function run(): void {
        foreach ($this->project->modules as $module) {
            $this->generateModule($module);
        }
    }

    protected function generateModule(ProjectModule $module): void {
        foreach ($module->tables as $table) {
            $this->generateTable($table);
        }
    }

    protected function generateTable(Table $table): void {
        throw new NotImplemented($this);
    }
}