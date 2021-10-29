<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Accounts\Account;
use Osm\Data\Accounts\AccountTable;
use Osm\Framework\TestCase;

class test_02_accounts extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;
    public bool $use_db = true;

    public function test_empty_table() {
        // GIVEN an empty table

        // WHEN you retrieve all records
        $result = AccountTable::new()->get();

        // THEN there are none
        $this->assertCount(0, $result->items);
    }

    public function test_dehydrated_insert() {
        // GIVEN an empty table

        // WHEN you insert a record
        $id = AccountTable::new()->dehydrated()->insert((object)[
            'type' => 'user',
            'email' => 'foo@bar.com',
        ]);

        // THEN it's there
        $result = AccountTable::new()->dehydrated()->get();
        $this->assertCount(1, $result->items);

        // AND dehydrated data is merged with explicit columns
        /* @var Account $account data class used as a hint class */
        $account = $result->first;
        $this->assertTrue($account->id === $id);
        $this->assertEquals('user', $account->type);
        $this->assertEquals('foo@bar.com', $account->email);
    }
}