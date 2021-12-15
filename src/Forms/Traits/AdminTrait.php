<?php

namespace Osm\Admin\Forms\Traits;

use Osm\Admin\Base\Attributes\Class_;
use Osm\Admin\Forms\Form;
use Osm\Admin\Interfaces\Interface_\Admin;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;

/**
 * @property Form $form #[Serialized]
 */
#[UseIn(Admin::class)]
trait AdminTrait
{
    protected function get_form(): Form {
        /* @var Admin|static $this */
        global $osm_app; /* @var App $osm_app */

        $className = Form::class;
        $classes = $osm_app->descendants->classes(Form::class);
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

        return $new(['interface' => $this]);
    }

    protected function around___wakeup(callable $proceed) {
        $proceed();

        $this->form->interface = $this;
    }
}