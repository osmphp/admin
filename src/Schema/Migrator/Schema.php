<?php

namespace Osm\Admin\Schema\Migrator;

use Osm\Admin\Schema\Migrator;
use Osm\Admin\Schema\Schema as SchemaObject;
use Osm\Core\Exceptions\Required;

class Schema extends Migrator
{
    /**
     * @var Migrator[]
     */
    public array $create_tables = [];

    /**
     * @var Migrator[]
     */
    public array $alter_tables = [];

    /**
     * @var Migrator[]
     */
    public array $rename_tables = [];

    /**
     * @var Migrator[]
     */
    public array $drop_tables = [];

    /**
     * @var Migrator[]
     */
    public array $drop_search_indexes = [];

    /**
     * @var Migrator[]
     */
    public array $create_notifications = [];

    /**
     * @var Migrator[]
     */
    public array $rename_all_notifications = [];

    /**
     * @var Migrator[]
     */
    public array $drop_notifications = [];

    /**
     * @var Migrator[]
     */
    public array $drop_all_notifications = [];

    /**
     * @var Migrator[]
     */
    public array $data_conversions = [];

    /**
     * indexer name => true
     *
     * @var bool[]
     */
    public array $indexers_to_be_rebuilt = [];

    public function migrate(): void
    {
        foreach ($this->create_tables as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->alter_tables as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->drop_tables as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->create_indexes as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->alter_indexes as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->drop_search_indexes as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->create_notifications as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->drop_notifications as $migrator) {
            $migrator->migrate();
        }

        foreach ($this->data_conversions as $migrator) {
            $migrator->migrate();
        }
    }
}