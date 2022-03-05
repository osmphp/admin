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
        $relatedId = $this->db->table('related_items')->insertGetId([]);

        $this->db->table('items')->insert([
            'record_id' => $relatedId,
            '_data' => json_encode((object)[
                'int' => 5,
                'float' => 10.0,
                'string' => 'Lorem ipsum',
                'bool' => true,
                'datetime' => Carbon::now(),
                'mixed' => 'Hello, world!',
                'object' => (object)[
                    'int' => 3,
                    'string' => 'One more string!',
                    'struct' => (object)[
                        'int' => 7,
                        'string' => 'Another string',
                    ],
                ],
                'int_array' => [1, 2, 3,],
            ]),
        ]);

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