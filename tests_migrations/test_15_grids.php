<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Grids\Column;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\Areas\Admin;
use Osm\Framework\TestCase;

class test_15_grids extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_scopes() {
        // GIVEN `Scope` PHP class
        $class = $this->app->schema->classes[Scope::class];

        // WHEN you check the definition of the main grid
        $key = Admin::class . ":/scopes/";
        $this->assertArrayHasKey($key, $class->grids);
        $grid = $class->grids[$key];

        // THEN it has columns specified in PHP attributes
        $this->assertArrayHasKey('id', $grid->columns);
        $this->assertInstanceOf(Column\Id::class,
            $grid->columns['id']);
        $this->assertArrayHasKey('title', $grid->columns);
        $this->assertInstanceOf(Column\String_::class,
            $grid->columns['title']);
    }
}