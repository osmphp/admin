<?php

namespace Osm\Admin\Filters\Traits;

use Osm\Admin\Base\Attributes\Markers\Filter as FilterMarker;
use Osm\Admin\Filters\Filter;
use Osm\Admin\Interfaces\Interface_;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;
/**
 * @property Filter[] $filters #[Serialized]
 */
#[UseIn(Interface_::class)]
trait InterfaceTrait
{
    protected function get_filters(): array {
        /* @var Interface_|static $this */
        global $osm_app; /* @var App $osm_app */

        $filters = [];

        foreach ($this->class->properties as $property) {
            foreach ($property->reflection->attributes as
                $attributeClassName => $attribute)
            {
                if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
                    continue;
                }

                /* @var FilterMarker $marker */
                if (!($marker = $class->attributes[FilterMarker::class] ?? null)) {
                    continue;
                }

                $new = "{$osm_app->classes[Filter::class]
                    ->getTypeClassName($marker->type ?? null)}::new";

                $data = array_filter((array)$attribute,
                    fn($item) => $item !== null);

                $filters[$property->name] = $new(array_merge(
                    ['interface' => $this], $data));
            }
        }

        return $filters;
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->filters as $filter) {
            $filter->interface = $this;
        }
    }
}