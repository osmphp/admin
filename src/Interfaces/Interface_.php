<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Class_ $class
 * @property string $url #[Serialized]
 * @property Parameter[] $parameters
 */
class Interface_ extends Object_
{
    use SubTypes;

    public function route(string $routeName): string
    {
        $pos = strpos($routeName, ' ') + 1;

        return substr($routeName, 0, $pos) . $this->url .
            substr($routeName, $pos);
    }

    public function url(string $routeName): string {
        global $osm_app; /* @var App $osm_app */

        if (($pos = strpos($routeName, ' ')) !== false) {
            $routeName = substr($routeName, $pos + 1);
        }

        return "{$osm_app->area_url}{$this->url}{$routeName}";
    }

    public function filterUrl(string $url, array $appliedFilters): string {
        $delimiter = mb_strpos($url, '?') !== false ? '&' : '?';

        foreach ($appliedFilters as $propertyName => $appliedFilter) {
            $url .= $delimiter;
            $url .= $this->class->filters[$propertyName]->url($appliedFilter);
            $delimiter = '&';
        }

        return $url;
    }

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        throw new Required(__METHOD__);
    }

    protected function get_area_class_name(): string {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->parameters as $parameter) {
            $parameter->interface = $this;
        }
    }

    protected function get_parameters(): array {
        return [];
    }
}