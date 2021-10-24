<?php

namespace Osm\Data\Tools\Generators\Commands;

use Osm\Framework\Console\Command;
use Osm\Data\Tools\Generators\Generator;
use Osm\Framework\Console\Attributes\Option;

/**
 * @property bool $admin #[Option]
 */
class GenerateSchema extends Command
{
    public string $name = 'generate:schema';

    public function run(): void
    {
        if ($this->admin) {
            Generator\Admin::new(['output' => $this->output])->run();
        }
    }
}