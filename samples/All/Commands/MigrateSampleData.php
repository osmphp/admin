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
            'qty' => 5,
        ]);

        query(Product::class)->insert([
            'sku' => 'DRESS',
            'title' => 'Blue Dress',
            'qty' => 3,
        ]);

        query(Product::class)->insert([
            'sku' => 'SHIRT',
            'title' => 'White shirt',
            'qty' => 20,
        ]);

        query(Product::class)->insert([
            'sku' => 'JACKET',
            'title' => 'Black Jacket',
            'qty' => 1,
        ]);
    }
}