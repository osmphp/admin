<?php

namespace Osm\Admin\Samples\Seed\Commands;

use Carbon\Carbon;
use Osm\Core\App;
use Osm\Framework\Console\Command;
use Osm\Framework\Db\Db;

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
            'data' => json_encode((object)[
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
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }
}