<?php

namespace Osm\Admin\Interfaces\Traits;

use Osm\Admin\Base\Attributes\Interface_\Operation as OperationAttribute;
use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Interfaces\Operation;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Admin\Base\Attributes\Markers\Interface_ as InterfaceMarker;

/**
 * @property Interface_[] $interfaces #[Serialized]
 * @property Operation[] $operations #[Serialized]
 */
#[UseIn(Class_::class)]
trait ClassTrait
{
    protected function get_interfaces(): array {
        global $osm_app; /* @var App $osm_app */
        /* @var Class_|\Osm\Admin\Grids\Traits\ClassTrait $this */

        $interfaces = [];

        foreach ($this->reflection->attributes as
            $attributeClassName => $attribute)
        {
            if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
                continue;
            }

            /* @var InterfaceMarker $marker */
            if (!($marker = $class->attributes[InterfaceMarker::class] ?? null)) {
                continue;
            }

            $new = "{$osm_app->classes[Interface_::class]
                ->getTypeClassName($marker->type ?? null)}::new";

            /* @var Interface_ $interface */
            $interface = $new(array_merge(['class' => $this], (array)$attribute));
            $interfaces[$interface->type] = $interface;
        }

        return $interfaces;
    }

    protected function get_operations(): array {
        global $osm_app; /* @var App $osm_app */
        /* @var Class_|\Osm\Admin\Grids\Traits\ClassTrait $this */

        $operations = [
            'insert' => Operation::new([
                'class' => $this,
                'name' => 'insert',
                'title' => 'Create',
            ]),
            'update' => Operation::new([
                'class' => $this,
                'name' => 'update',
                'title' => 'Edit',
            ]),
            'delete' => Operation::new([
                'class' => $this,
                'name' => 'delete',
                'title' => 'Delete',
            ]),
        ];

        /* @var OperationAttribute[] $attributes */
        $attributes = $this->reflection->attributes[OperationAttribute::class]
            ?? [];

        foreach ($attributes as $attribute) {
            if (!$attribute->enabled) {
                if (isset($operations[$attribute->name])) {
                    unset($operations[$attribute->name]);
                }
                continue;
            }

            $data = (array)$attribute;
            unset($data['enabled']);

            if (isset($operations[$attribute->name])) {
                foreach ($data as $key => $value) {
                    $operations[$attribute->name]->$key = $value;
                }
                continue;
            }

            $operations[$attribute->name] = Operation::new(array_merge(
                ['class' => $this], $data));
        }

        return $operations;
    }

    protected function around___wakeup(callable $proceed): void {
        $proceed();

        foreach ($this->interfaces as $interface) {
            $interface->class = $this;
        }

        foreach ($this->operations as $operation) {
            $operation->class = $this;
        }
    }

}