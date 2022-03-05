<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\List_;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\UseIn;

/**
 * @property string $url #[Serialized]
 * @property List_[] $list_views #[Serialized]
 * @property Form $form_view #[Serialized]
 *
 * @uses Serialized
 */
#[UseIn(Struct::class)]
trait StructTrait
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
                'struct' => $this,
                'name' => 'grid',
                'selects' => ['title'],
            ]),
        ];
    }

    protected function get_form_view(): Form {
        /* @var Table|static $this */

        return Form::new([
            'name' => 'form',
            'struct' => $this,
        ]);
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->list_views as $view) {
            $view->struct = $this;
        }

        $this->form_view->struct = $this;
    }
}