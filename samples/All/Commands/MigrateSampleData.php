<?php

namespace Osm\Admin\Samples\All\Commands;

use Osm\Admin\Samples\Products\Product;
use Osm\Framework\Console\Command;
use function Osm\query;

class MigrateSampleData extends Command
{
    public string $name = 'migrate:sample-data';

    public function run(): void
    {
        query(Product::class)->insert([
            'sku' => 'BAG',
            'title' => 'Pink Bag',
        ]);

        query(Product::class)->insert([
            'sku' => 'DRESS',
            'title' => 'Blue Dress',
        ]);
    }
}