<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\TestCase;
use function Osm\__;
use function Osm\query;

class test_02_scopes extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;

    public function test_inserts() {
        // GIVEN a database with the schema migrated, and the root scope created
        /* @var \stdClass|Scope $root */
        $rootId = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->value('id');

        // WHEN you create a child scope
        $retailId = query(Scope::class)->insert([
            'title' => __('Retail'),
            'parent_id' => $rootId,
        ]);

        // THEN it inherits values from the root scope as expected
        $this->assertEquals(1, $this->app->db->table('scopes')
            ->where('id', $retailId)
            ->value('level'));
        $this->assertTrue($this->app->db->table('scopes')
                ->where('id', $retailId)
                ->value('id_path') === "{$rootId}/{$retailId}");

        // clear the database
        query(Scope::class)->equals('id', $retailId)->delete();
    }

    public function test_joins() {
        // GIVEN a database with the schema migrated, and the root scope created
        $root = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->value('id');

        // AND 2 levels of children are created
        $child1 = query(Scope::class)->insert([
            'title' => __('Child 1'),
            'parent_id' => $root,
        ]);
        $child2 = query(Scope::class)->insert([
            'title' => __('Child 2'),
            'parent_id' => $child1,
        ]);

        // WHEN you select joined columns
        /* @var \stdClass|Scope $scope */
        $scope = query(Scope::class)
            ->equals('parent.id', $child1)
            ->first(
                'id',
                'parent.id',
                'parent.parent.id',
                'parent.parent.parent.id',
                'parent.parent.parent.parent.id',
            );

        // THEN the returned object contains child objects
        // recursively retrieved from the joined tables
        $this->assertTrue($scope->id === $child2);
        $this->assertTrue($scope->parent->id === $child1);
        $this->assertTrue($scope->parent->parent->id === $root);
        $this->assertTrue(!isset($scope->parent->parent->parent));

        // clear the database
        query(Scope::class)->equals('id', $child1)->delete();
    }

    public function test_updates() {
        // GIVEN a database with the schema migrated, and the root scope created
        /* @var \stdClass|Scope $root */
        $rootId = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->value('id');

        // AND a child, and a grand child scope
        $retailId = query(Scope::class)->insert([
            'title' => __('Retail'),
            'parent_id' => $rootId,
        ]);
        $englishId = query(Scope::class)->insert([
            'title' => __('English'),
            'parent_id' => $retailId,
        ]);

        // WHEN you move `English` scope directly under the `Global` scope
        query(Scope::class)
            ->equals('id', $englishId)
            ->update(['parent_id' => $rootId]);

        // THEN its level and id_path are recalculated, too
        $this->assertEquals(1, $this->app->db->table('scopes')
            ->where('id', $englishId)
            ->value('level'));
        $this->assertTrue($this->app->db->table('scopes')
                ->where('id', $englishId)
                ->value('id_path') === "{$rootId}/{$englishId}");

        // clear the database
        query(Scope::class)->equals('id', $retailId)->delete();
        query(Scope::class)->equals('id', $englishId)->delete();
    }

    public function test_cascade_updates() {
        // GIVEN a database with the schema migrated, and the root scope created
        $root = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->value('id');

        // AND 3 levels of children are created
        $child1 = query(Scope::class)->insert([
            'title' => __('Child 1'),
            'parent_id' => $root,
        ]);
        $child2 = query(Scope::class)->insert([
            'title' => __('Child 2'),
            'parent_id' => $child1,
        ]);
        $child3 = query(Scope::class)->insert([
            'title' => __('Child 3'),
            'parent_id' => $child2,
        ]);

        // WHEN you move `Child 2` scope directly under the `Global` scope
        query(Scope::class)
            ->equals('id', $child2)
            ->update(['parent_id' => $root]);

        // THEN its child's level and id_path are recalculated, too
        $this->assertEquals(2, $this->app->db->table('scopes')
            ->where('id', $child3)
            ->value('level'));
        $this->assertTrue($this->app->db->table('scopes')
                ->where('id', $child3)
                ->value('id_path') === "{$root}/{$child2}/{$child3}");

        // clear the database
        query(Scope::class)->equals('id', $child1)->delete();
        query(Scope::class)->equals('id', $child2)->delete();
    }

}