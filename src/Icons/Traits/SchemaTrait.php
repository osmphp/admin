<?php

namespace Osm\Admin\Icons\Traits;

use Osm\Admin\Base\Attributes\Icon as IconAttribute;
use Osm\Admin\Icons\Icon;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property Icon[] $icons #[Serialized]
 */
#[UseIn(Schema::class)]
trait SchemaTrait
{
    protected function get_icons(): array {
        global $osm_app; /* @var App $osm_app */
        /* @var Schema|static $this */

        $icons = [];
        foreach ($this->classes as $class) {
            /* @var IconAttribute[] $attributes */
            if (!($attributes = $class->reflection->attributes[IconAttribute::class]
                ?? null))
            {
                continue;
            }

            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            foreach ($attributes as $attribute) {
                $icons[$attribute->url] = Icon::new(
                    array_merge(['schema' => $this], (array)$attribute));
            }
        }

        return $icons;
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->icons as $icon) {
            $icon->schema = $this;
        }
    }
}