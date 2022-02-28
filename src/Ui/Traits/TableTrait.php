<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\List_;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\UseIn;

/**
 * @property string $url
 * @property List_[] $list_views #[Serialized]
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

    protected function get_list_views(): array {
        /* @var Table|static $this */

        return [
            'grid' => List_\Grid::new([
                'table' => $this,
                'name' => 'grid',
                'selects' => ['title'],
            ]),
        ];
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->list_views as $listView) {
            $listView->table = $this;
        }
    }
}