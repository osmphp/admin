<?php

declare(strict_types=1);

namespace Osm\Admin\Schema\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;

/**
 * @property Db $db
 */
class M02_indexers extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function create(): void {
        $this->db->create('indexers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->boolean('requires_partial_reindex')
                ->default(false);
            $table->boolean('requires_full_reindex')
                ->default(true);

        });
    }

    public function drop(): void {
        $this->db->drop('indexers');
    }
}