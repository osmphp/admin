<?php

declare(strict_types=1);

namespace Osm\Admin\Schema;

use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\BaseModule;
use Osm\Framework\Cache\Attributes\Cached;

/**
 * @property DataType[] $data_types #[Cached('data_types')]
 *
 * @uses Cached
 */
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Framework\All\Module::class,
    ];

    protected function get_data_types(): array {
        global $osm_app; /* @var App $osm_app */

        return array_map(function(string $className) {
            $new = "{$className}::new";

            return $new();
        }, $osm_app->descendants->byName(DataType::class,
            Type::class));
    }
}