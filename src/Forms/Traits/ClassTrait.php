<?php

namespace Osm\Admin\Forms\Traits;

use Osm\Admin\Forms\Form;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Base\Attributes\Markers\Form as FormMarker;

/**
 * @property Form[] $forms
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_forms(): array {
        /* @var Class_|\Osm\Admin\Grids\Traits\ClassTrait $this */
        $forms = [];

        foreach ($this->reflection->attributes as $className => $attributes) {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            $forms = array_merge($forms,
                $this->createForms($className, $attributes));
        }

        return $forms;
    }

    protected function createForms(string $attributeClassName,
        array $attributes): array
    {
        global $osm_app; /* @var App $osm_app */
        /* @var Class_|static $this */

        if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
            return [];
        }

        /* @var FormMarker $marker */
        if (!($marker = $class->attributes[FormMarker::class] ?? null)) {
            return [];
        }

        $formClassNames = $osm_app->descendants->byName(Form::class);
        $new = "{$formClassNames[$marker->type]}::new";

        $forms = [];

        foreach ($attributes as $attribute) {
            $form = $new(array_merge(['data_class_name' => $this->name],
                (array)$attribute));
            $forms[$form->name] = $form;
        }

        return $forms;
    }
}