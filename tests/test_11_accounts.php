<?php

declare(strict_types=1);

namespace Osm\Admin\Tests;

use Osm\Admin\Accounts\Account;
use Osm\Admin\Accounts\Accounts;
use Osm\Framework\TestCase;

class test_11_accounts extends TestCase
{
    public string $app_class_name = \Osm\Admin\Samples\App::class;
    public bool $use_db = true;

    public function test_empty_table() {
        // GIVEN an empty table

        // WHEN you retrieve all records
        $result = Accounts::new()->get();

        // THEN there are none
        $this->assertCount(0, $result->items);
    }

    public function test_dehydrated_insert() {
        // GIVEN an empty table

        // WHEN you insert a record
        $id = Accounts::new()->dehydrated()->insert((object)[
            'type' => 'user',
            'email' => 'foo@bar.com',
        ]);

        // THEN it's there
        $result = Accounts::new()->dehydrated()->get();
        $this->assertCount(1, $result->items);

        // AND `data` is merged with explicit columns
        /* @var Account $account data class used as a hint class */
        $account = $result->first;
        $this->assertTrue($account->id === $id);
        $this->assertEquals('user', $account->type);
        $this->assertEquals('foo@bar.com', $account->email);
    }
}