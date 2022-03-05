<?php

namespace Osm\Admin\Tests;

use Osm\Admin\Samples\Products\Color;
use Osm\Framework\TestCase;

class test_01_reflection_experiments extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_enum(): void {
        // GIVEN the definition of the `Color` enum
        $enum = new \ReflectionEnum(Color::class);

        // WHEN you reflect over `Pink` case
        $pink = $enum->getCase('Pink');
        $attribute = $pink->getAttributes()[0]->newInstance();

        // THEN you can read its attributes
        $this->assertEquals('Pink', $attribute->name);
    }
}