<?php

namespace Osm\Admin\Storages\Traits;

use Osm\Admin\Storages\Storage;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Admin\Base\Attributes\Markers\Storage as QueryMarker;

/**
 * @property ?Storage $storage
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_storage(): ?Storage {
        global $osm_app; /* @var App $osm_app */
        /* @var Class_|static $this */

        foreach ($this->reflection->attributes as
            $attributeClassName => $attribute)
        {
            if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
                continue;
            }

            /* @var QueryMarker $marker */
            if (!($marker = $class->attributes[QueryMarker::class] ?? null)) {
                continue;
            }

            $new = "{$osm_app->classes[Storage::class]
                ->getTypeClassName($marker->type ?? null)}::new";

            return $new(array_merge(['class' => $this], (array)$attribute));
        }

        return null;
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        if ($this->storage) {
            $this->storage->class = $this;
        }
    }
}