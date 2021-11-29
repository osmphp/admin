<?php

namespace Osm\Admin\Formulas;

use Osm\Core\BaseModule;

/**
 * @property Parser $parser
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Schema\Module::class,
    ];

    protected function get_parser(): Parser {
        return Parser::new();
    }
}