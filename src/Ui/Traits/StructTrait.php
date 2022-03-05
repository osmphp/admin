<?php

namespace Osm\Admin\Ui\Traits;

use Illuminate\Support\Str;
use Osm\Admin\Schema\Attributes\Class_;
use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Attributes\View;
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
        global $osm_app; /* @var App $osm_app */

        /* @var Table|static $this */

        $views = [];

        $classes = $osm_app->descendants->classes(List_::class);
        foreach ($classes as $class) {
            /* @var Class_ $classAttribute */
            if (!($classAttribute = $class->attributes[Class_::class] ?? null)) {
                continue;
            }

            if ($classAttribute->class_name != $this->name) {
                continue;
            }

            /* @var View $viewAttribute */
            if (!($viewAttribute = $class->attributes[View::class] ?? null)) {
                continue;
            }

            if ($viewAttribute->name !== 'list') {
                continue;
            }

            $new = "{$class->name}::new";
            $name = Str::snake(mb_substr($class->name,
                mb_strrpos($class->name, '\\') + 1));

            $views[$name] = $new([
                'struct' => $this,
                'name' => $name,
            ]);
        }

        if (!isset($views['grid'])) {
            $views['grid'] = List_\Grid::new([
                'struct' => $this,
                'name' => 'grid',
            ]);
        }

        return $views;
    }

    protected function get_form_view(): Form {
        /* @var Table|static $this */

        return Form::new([
            'struct' => $this,
            'name' => 'form',
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