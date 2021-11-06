<?php

declare(strict_types=1);

namespace Osm\Admin\TestsMigrations;

use Osm\Admin\Forms\Form;
use Osm\Admin\Grids\Column;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\Areas\Admin;
use Osm\Framework\TestCase;
use Osm\Admin\Forms\Field;

class test_03_forms extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_scopes_create() {
        // GIVEN `Scope` PHP class
        $class = $this->app->schema->classes[Scope::class];

        // WHEN you check the definition of the main grid
        $key = Admin::class . ":/scopes/create";
        $this->assertArrayHasKey($key, $class->forms);
        $form = $class->forms[$key];

        // THEN it has the `implicit` chapter
        $this->assertArrayHasKey('implicit', $form->chapters);
        $chapter = $form->chapters['implicit'];

        // AND the chapter has the `implicit` section
        $this->assertArrayHasKey('implicit', $chapter->sections);
        $section = $chapter->sections['implicit'];

        // AND the section has the `implicit` group
        $this->assertArrayHasKey('implicit', $section->groups);
        $group = $section->groups['implicit'];

        // AND the group has fields specified in PHP attributes
        $this->assertArrayHasKey('title', $group->fields);
        $this->assertInstanceOf(Field\String_::class,
            $group->fields['title']);
    }
}