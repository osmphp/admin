<?php

namespace Osm\Admin\Ui\Traits;

use Osm\Admin\Schema\Class_\Table;
use Osm\Admin\Ui\Attributes\Ui;
use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\Attributes\UseIn;
use Osm\Framework\Areas\Attributes\Area;
use Osm\Framework\Http\Module;
use Osm\Framework\Http\Route;

#[UseIn(Module::class)]
trait HttpModuleTrait
{
    protected function around_loadRoutes(callable $proceed): void {
        global $osm_app; /* @var App $osm_app */

        // first, find all statically defined routes, and put them into
        // `routes` and `dynamic_routes` properties. Note that these
        // properties are `#Cached`, so this only happens once
        $proceed();

        foreach ($osm_app->schema->tables as $table) {
            $this->loadUiRoutes($table);
        }
    }

    protected function loadUiRoutes(Table $class): void
    {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $self */
        $self = $this;

        $routeClasses = $osm_app->descendants->classes(Route::class);
        foreach ($routeClasses as $routeClass) {
            /* @var Ui $ui */
            if (!($ui = $routeClass->attributes[Ui::class] ?? null)) {
                continue;
            }

            /* @var Name $name */
            if (!($name = $routeClass->attributes[Name::class] ?? null)) {
                continue;
            }

            if (!isset($self->routes[$ui->area_class_name])) {
                $self->routes[$ui->area_class_name] = [];
            }

            $self->routes[$ui->area_class_name][$class->route($name->name)] = [
                $routeClass->name => [
                    'route_name' => $name->name,
                    'class_name' => $class->name,
                ],
            ];
        }
    }

}