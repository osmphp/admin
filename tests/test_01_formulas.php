<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

use Osm\Admin\Formulas\Formula;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\TestCase;
use function Osm\formula;
use function Osm\query;

class test_01_formulas extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    function test_identifiers() {
        // GIVEN a definition of `Scope` class

        // WHEN you parse a qualifier identifier
        $formula = formula('parent.parent.title',
            $this->app->schema->classes[Scope::class]);

        // THEN the parsed information contains resolved property information
        $this->assertInstanceOf(Formula\Identifier::class, $formula);
        $this->assertCount(2, $formula->accessors);
        $this->assertEquals('parent', $formula->accessors[0]->name);
        $this->assertEquals('parent', $formula->accessors[1]->name);
        $this->assertEquals('title', $formula->property->name);
    }

    public function test_wildcard() {
        // GIVEN a scope query
        $query = query(Scope::class);

        // WHEN you select *
        $query->select('*');

        // THEN it actually selects all properties that don't require
        // joins or sub-selects
        $this->assertArrayHasKey('id', $query->selects);
        $this->assertArrayNotHasKey('parent', $query->selects);
        $this->assertArrayNotHasKey('children', $query->selects);
    }
}