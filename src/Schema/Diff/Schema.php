<?php

namespace Osm\Admin\Schema\Diff;

use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\NotificationTable as NotificationTableObject;
use Osm\Admin\Schema\Schema as SchemaObject;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Symfony\Component\Console\Output\OutputInterface;
use function Osm\__;

/**
 * @property \stdClass|SchemaObject|null $old
 * @property SchemaObject $new
 */
class Schema extends Diff
{
    /**
     * @var Table[]
     */
    protected array $tables = [];

    /**
     * @var NotificationTable[]
     */
    protected array $notification_tables = [];

    /**
     * @var string[]
     */
    protected array $dropped_notification_tables = [];

    protected function get_new(): SchemaObject {
        throw new Required(__METHOD__);
    }

    public function table(TableObject $table): Table {
        if (!isset($this->tables[$table->name])) {
            if ($table->rename) {
                $name = $table->rename;
                if (!isset($this->old->tables->$name)) {
                    if (isset($this->old->tables->{$table->name})) {
                        // once #[Rename] migrated, during another migration,
                        // "old" schema will already contain new name.
                        $name = $table->name;
                    }
                    else {
                        throw new InvalidRename(__(
                            "Previous schema doesn't contain the ':old_name' table referenced in the #[Rename] attribute of the ':new_name' table.", [
                                'old_name' => $table->rename,
                                'new_name' => $table->name,
                            ]
                        ));
                    }
                }
            }
            else {
                $name = $table->name;
            }

            $this->tables[$table->name] = Table::new([
                'old' => $this->old->tables->$name ?? null,
                'new' => $table,
                'schema' => $this,
                'output' => $this->output,
                'dry_run' => $this->dry_run,
            ]);
        }

        return $this->tables[$table->name];
    }

    public function migrate(): void {
        foreach ($this->tables as $table) {
            $table->migrate();
        }

        foreach ($this->dropped_notification_tables as $table) {
            $this->drop($table);
        }

        foreach ($this->notification_tables as $table) {
            $table->migrate();
        }
    }

    protected function drop(string $table): void {
        $this->db->drop($table);
    }

    public function notificationTable(NotificationTableObject $table)
        : NotificationTable
    {
        if (!isset($this->notification_tables[$table->name])) {
            if ($table->rename) {
                $name = $table->rename;
                if (!isset($this->old->notification_tables->$name)) {
                    $name = $table->name;
                }
            }
            else {
                $name = $table->name;
            }

            $this->notification_tables[$table->name] = NotificationTable::new([
                'old' => $this->old->notification_tables->$name ?? null,
                'new' => $table,
                'schema' => $this,
                'output' => $this->output,
                'dry_run' => $this->dry_run,
            ]);
        }

        return $this->notification_tables[$table->name];
    }

    public function diff(): void
    {
        foreach ($this->new->tables as $table) {
            $this->table($table)->diff();
        }

        foreach ($this->new->notification_tables as $table) {
            $this->notificationTable($table)->diff();
        }

        if ($this->old) {
            foreach ($this->old->tables as $table) {
                $this->planDroppingTable($table);
            }

            foreach ($this->old->notification_tables as $table) {
                $this->planDroppingNotificationTable($table);
            }
        }
    }

    protected function planDroppingTable(\stdClass|Table $table): void {
        //throw new NotImplemented($this);
    }

    protected function planDroppingNotificationTable(
        \stdClass|NotificationTable $table): void
    {
        //throw new NotImplemented($this);
    }

}