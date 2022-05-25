<?php

namespace Osm\Admin\Samples\Seed\Commands;

use Carbon\Carbon;
use Osm\Admin\Samples\Products\Color;
use Osm\Admin\Samples\Products\Product;
use Osm\Core\App;
use Osm\Framework\Console\Command;
use Osm\Framework\Db\Db;
use function Osm\query;

/**
 * @property Db $db
 */
class Samples extends Command
{
    public string $name = 'migrate:samples';

    public function run(): void
    {
        query(Product::class)->insert([
            'title' => 'Pink Bag',
            'color' => Color::PINK,
        ]);
        query(Product::class)->insert([
            'title' => 'Blue Dress',
            'color' => Color::BLUE,
        ]);
        query(Product::class)->insert([
            'title' => 'White Shirt',
            'color' => Color::WHITE,
        ]);
        query(Product::class)->insert([
            'title' => 'Black Jacket',
            'color' => Color::BLACK,
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }
}