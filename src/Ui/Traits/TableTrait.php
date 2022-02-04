<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Class_\Table;
use Osm\Core\Attributes\UseIn;

/**
 * @property string $url
 */
#[UseIn(Table::class)]
trait TableTrait
{
    public function route(string $routeName): string
    {
        $pos = strpos($routeName, ' ') + 1;

        return substr($routeName, 0, $pos) . $this->url .
            substr($routeName, $pos);
    }

    protected function get_url(): string {
        /* @var Table|static $this */
        return '/' .
            str_replace(' ', '-', $this->s_objects_lowercase);
    }
}