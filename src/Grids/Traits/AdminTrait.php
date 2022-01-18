<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Base\Attributes\Class_;
use Osm\Admin\Base\Attributes\Grid as GridAttribute;
use Osm\Admin\Forms\Form;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Interfaces\Interface_\Admin;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property Grid $grid #[Serialized]
 */
#[UseIn(Admin::class)]
trait AdminTrait
{
    protected function get_grid(): Grid {
        /* @var Admin|static $this */
        global $osm_app; /* @var App $osm_app */

        $className = Grid::class;
        $classes = $osm_app->descendants->classes(Grid::class);
        foreach ($classes as $class) {
            /* @var Class_ $specific */
            if (!($specific = $class->attributes[Class_::class] ?? null)) {
                continue;
            }

            if ($specific->class_name === $this->class->name) {
                $className = $class->name;
                break;
            }
        }

        $new = "{$className}::new";

        $data = ['interface' => $this];

        if ($attribute = $this->class->reflection
            ->attributes[GridAttribute::class] ?? null)
        {
            $data = array_merge($data, (array)$attribute);
        }

        return $new($data);
    }

    protected function around___wakeup(callable $proceed) {
        $proceed();

        $this->grid->interface = $this;
    }
}