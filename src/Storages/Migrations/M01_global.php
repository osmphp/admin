<?php

declare(strict_types=1);

namespace Osm\Admin\Storages\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;

/**
 * @property Db $db
 */
class M01_global extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function create(): void {
        $this->db->create('global_', function (Blueprint $table) {
            $table->json('schema')->nullable();
        });
    }

    public function insert(): void
    {
        $this->db->table('global_')->insert(['schema' => null]);
    }

    public function drop(): void {
        $this->db->drop('global_');
    }
}