<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Osm\Admin\Accounts\Account;
use Osm\Admin\Accounts\Accounts;
use Osm\Admin\Scopes\Scope;
use Osm\Framework\TestCase;
use function Osm\__;
use function Osm\query;

class test_01_scopes extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;
    public bool $use_db = true;

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
            ->raw(fn(QueryBuilder $q) => $q->where('id', $englishId))
            ->update(['parent_id' => $rootId]);

        // THEN its level and id_path are recalculated, too
        $this->assertEquals(1, $this->app->db->table('scopes')
            ->where('id', $englishId)
            ->value('level'));
        $this->assertTrue($this->app->db->table('scopes')
                ->where('id', $englishId)
                ->value('id_path') === "{$rootId}/{$englishId}");
    }

}