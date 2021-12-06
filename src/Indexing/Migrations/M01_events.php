<?php

declare(strict_types=1);

namespace Osm\Admin\Indexing\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;

/**
 * @property Db $db
 */
class M01_events extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function create(): void {
        $this->db->create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('indexer');
            $table->string('source');
            $table->string('table');
            $table->unique(['indexer', 'source']);
            $table->boolean('changed')->default(false);
            $table->boolean('dirty')->default(true);
        });
    }

    public function drop(): void {
        $this->db->drop('events');
    }
}