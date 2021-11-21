<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

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
        $root = $this->app->db->table('scopes')
            ->whereNull('parent_id')
            ->first();

        // WHEN you create a child scope
        $retailId = query(Scope::class)->insert((object)[
            'title' => __('Retail'),
            'parent_id' => $root->id,
        ]);

        // THEN it inherits values from the root scope as expected
        $this->assertTrue($this->app->db->table('scopes')
            ->where('id', $retailId)
            ->value('level') === $root->level + 1);
        $this->assertTrue($this->app->db->table('scopes')
                ->where('id', $retailId)
                ->value('id_path') === "{$root->id}/{$retailId}");
    }
}