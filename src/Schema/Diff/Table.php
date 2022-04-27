<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Core\App;
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
 * @property bool $requires_alter
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
                        throw new InvalidRename(__(
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

            $this->properties[$property->name] = Property::new([
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

    protected function create(): void {
        $this->db->create($this->new->table_name, function(Blueprint $table) {
            foreach ($this->properties as $property) {
                $property->migrate($table);
            }

            $table->json('_data')->nullable();
            $table->json('_overrides')->nullable();
        });
    }

    protected function alter(): void {
        if ($this->requires_alter) {
            $this->db->alter($this->new->table_name, function(Blueprint $table) {
                foreach ($this->properties as $property) {
                    $property->migrate($table);
                }
            });
        }
    }

    protected function get_requires_alter(): bool {
        throw new NotImplemented($this);
    }
}