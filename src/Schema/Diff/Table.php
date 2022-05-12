<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Monolog\Logger;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Exceptions\InvalidChange;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Core\App;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Db\Db;
use function Osm\__;

/**
 * @property Schema $schema
 * @property \stdClass|TableObject|null $old
 * @property TableObject $new
 * @property bool $alter
 * @property ?string $rename
 * @property bool $requires_pre_alter `true` if any property diff contributes
 *      changes to the database table structure in pre-alter phase
 * @property bool $requires_post_alter `true` if any property diff contributes
 *      changes to the database table structure in post-alter phase
 * @property bool $requires_convert `true` if any property diff requires
 *      existing data conversion
 * @property Logger $log
 */
class Table extends Diff
{
    /**
     * @var Property[]
     */
    protected array $properties = [];

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_new(): TableObject {
        throw new Required(__METHOD__);
    }

    protected function get_alter(): bool {
        throw new Required(__METHOD__);
    }

    protected function get_rename(): ?string {
        throw new Required(__METHOD__);
    }

    public function property(PropertyObject $property): Property {
        global $osm_app; /* @var App $osm_app */

        if (!isset($this->properties[$property->name])) {
            if ($property->rename) {
                $name = $property->rename;
                if (!isset($this->old->properties->$name)) {
                    if (isset($this->old->properties->{$property->name})) {
                        // once #[Rename] migrated, during another migration,
                        // "old" schema will already contain new name.
                        $name = $property->name;
                    }
                    else {
                        throw new InvalidChange(__(
                            "Previous schema of ':table' table doesn't contain the ':old_name' property referenced in the #[Rename] attribute of the ':new_name' property.", [
                                'table' => $this->new->name,
                                'old_name' => $property->rename,
                                'new_name' => $property->name,
                            ]
                        ));
                    }
                }
            }
            else {
                $name = $property->name;
            }

            $classNames = $osm_app->descendants->byName(Property::class,
                Type::class);
            $new = "{$classNames[$property->type]}::new";

            $this->properties[$property->name] = $new([
                'old' => $this->old->properties->$name ?? null,
                'new' => $property,
                'table' => $this,
                'output' => $this->output,
                'dry_run' => $this->dry_run,
            ]);
        }

        return $this->properties[$property->name];
    }

    public function migrate(): void {
        if ($this->alter) {
            $this->alter();
        }
        else {
            $this->create();
        }
    }

    protected function preAlter(): void {
        if ($this->requires_pre_alter) {
            $this->log(__("Pre-altering ':table' table", [
                'table' => $this->new->table_name,
            ]));

            $this->db->alter($this->new->table_name, function(Blueprint $table) {
                foreach ($this->properties as $property) {
                    $property->migrate(Property::PRE_ALTER, $table);
                }
            });
        }
    }

    protected function convert(): void {
        $query = Query::new(['table' => $this->new]);

        if ($this->requires_convert) {
            $this->log(__("Converting ':table' table", [
                'table' => $this->new->table_name,
            ]));

            foreach ($this->properties as $property) {
                $property->convert($query);
            }

            $query->bulkUpdate();
        }
    }

    protected function validate(): void {
        //throw new NotImplemented($this);
    }

    protected function postAlter(): void {
        if ($this->requires_post_alter) {
            $this->log(__("Post-altering ':table' table", [
                'table' => $this->new->table_name,
            ]));

            $this->db->alter($this->new->table_name, function(Blueprint $table) {
                foreach ($this->properties as $property) {
                    $property->migrate(Property::POST_ALTER, $table);
                }
            });
        }
    }

    protected function create(): void {
        $this->log(__("Creating ':table' table", [
            'table' => $this->new->table_name,
        ]));

        $this->db->create($this->new->table_name, function(Blueprint $table) {
            foreach ($this->properties as $property) {
                $property->migrate(Property::CREATE, $table);
            }

            $this->log(__("    Creating system columns: '_data', `_overrides`"));

            $table->json('_data')->nullable();
            $table->json('_overrides')->nullable();
        });
    }

    protected function alter(): void {
        $this->preAlter();
        $this->convert();
        $this->validate();
        $this->postAlter();
    }

    protected function get_requires_pre_alter(): bool {
        foreach ($this->properties as $property) {
            if ($property->requiresMigration(Property::PRE_ALTER)) {
                return true;
            }
        }

        return false;
    }

    protected function get_requires_post_alter(): bool {
        foreach ($this->properties as $property) {
            if ($property->requiresMigration(Property::POST_ALTER)) {
                return true;
            }
        }

        return false;
    }

    protected function get_requires_convert(): bool {
        foreach ($this->properties as $property) {
            if ($property->convert()) {
                return true;
            }
        }

        return false;
    }

    public function diff(): void {
        $this->alter = $this->old != null;
        $this->rename = $this->old &&
            $this->new->table_name !== $this->old->table_name
                ? $this->old->table_name
                : null;

        foreach ($this->new->properties as $property) {
            $this->property($property)->diff();
        }

        if ($this->old) {
            foreach ($this->old->properties as $property) {
                $this->planDroppingProperty($property);
            }
        }
    }

    protected function planDroppingProperty(\stdClass|Property $property): void {
        //throw new NotImplemented($this);
    }

    protected function log(string $message): void {
        $this->log->notice($message);
    }

    protected function get_log(): Logger {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->logs->migrations;
    }
}