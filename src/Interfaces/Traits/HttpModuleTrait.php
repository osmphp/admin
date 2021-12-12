<?php

namespace Osm\Admin\Interfaces\Traits;

use Osm\Admin\Base\Attributes\Route\Class_;
use Osm\Admin\Base\Attributes\Route\Interface_ as InterfaceAttribute;
use Osm\Admin\Base\Attributes\Route\Operation;
use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Interfaces\Route;
use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Http\Module;
use function Osm\merge;

#[UseIn(Module::class)]
trait HttpModuleTrait
{
    protected function around_loadRoutes(callable $proceed): void {
        global $osm_app; /* @var App $osm_app */

        // first, find all statically defined routes, and put them into
        // `routes` and `dynamic_routes` properties. Note that these
        // properties are `#Cached`, so this only happens once
        $proceed();

        foreach ($osm_app->schema->classes as $class) {
            foreach ($class->interfaces as $interface) {
                $this->loadInterfaceRoutes($interface);
            }
        }
    }

    protected function loadInterfaceRoutes(Interface_ $interface): void {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $self */
        $self = $this;

        /* @var Area $area */
        if (!($area = $interface->__class->attributes[Area::class][0] ?? null)) {
            return;
        }

        $genericRoutes = [];
        $specificRoutes = [];

        $classes = $osm_app->descendants->classes(Route::class);
        foreach ($classes as $class) {
            /* @var InterfaceAttribute $attribute */
            if (!($attribute = $class->attributes[InterfaceAttribute::class]
                ?? null))
            {
                continue;
            }

            if (!is_a($attribute->class_name, $interface->__class->name,
                true))
            {
                continue;
            }

            /* @var Name $name */
            if (!($name = $class->attributes[Name::class] ?? null)) {
                continue;
            }

            /* @var Operation $operation */
            if ($operation = $class->attributes[Operation::class] ?? null) {
                if (!isset($interface->class->operations[$operation->name])) {
                    continue;
                }
            }

            /* @var Class_ $specific */
            if ($specific = $class->attributes[Class_::class] ?? null) {
                if ($interface->class->name === $specific->class_name) {
                    $specificRoutes[$name->name] = $class->name;
                }
            }
            else {
                $genericRoutes[$name->name] = $class->name;
            }
        }

        foreach (array_merge($genericRoutes, $specificRoutes) as
            $routeName => $routeClassName)
        {
            if (!isset($self->routes[$area->class_name])) {
                $self->routes[$area->class_name] = [];
            }

            $self->routes[$area->class_name][$interface->route($routeName)] = [
                $routeClassName => [
                    'class_name' => $interface->class->name,
                    'interface_type' => $interface->type,
                ],
            ];
        }
    }
}