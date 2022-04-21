<?php

namespace Osm\Admin\Schema\Migrator;

use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Admin\Schema\Migrator;
use Osm\Admin\Schema\Schema as SchemaObject;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Core\Exceptions\Required;
use Symfony\Component\Console\Output\OutputInterface;
use function Osm\__;

/**
 * @property \stdClass|SchemaObject|null $old
 * @property SchemaObject $new
 */
class Schema extends Migrator
{
    protected array $tables = [];

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
}