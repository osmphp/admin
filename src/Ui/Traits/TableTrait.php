<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Grid;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $url
 * @property Grid $grid #[Serialized]
 *
 * @uses Serialized
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

    public function url(string $routeName): string {
        global $osm_app; /* @var App $osm_app */

        if (($pos = strpos($routeName, ' ')) !== false) {
            $routeName = substr($routeName, $pos + 1);
        }

        return "{$osm_app->area_url}{$this->url}{$routeName}";
    }

    protected function get_grid(): Grid {
        /* @var Table|static $this */

        return Grid::new([
            'table' => $this,
            'select_identifiers' => $this->select_identifiers,
        ]);
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();
        $this->grid->table = $this;
    }
}