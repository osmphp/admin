<?php

namespace Osm {

    use Osm\Admin\Formulas\Formula;
    use Osm\Admin\Formulas\Module;
    use Osm\Admin\Schema\Class_;
    use Osm\Core\App;

    function formula(string $text, Class_ $class): Formula {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        return $module->parser->parse($text, $class);
    }
}