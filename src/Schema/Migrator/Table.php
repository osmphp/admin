<?php

namespace Osm\Admin\Schema\Migrator;

use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Admin\Schema\Migrator;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Table as TableObject;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property Schema $schema
 * @property \stdClass|TableObject|null $old
 * @property TableObject $new
 */
class Table extends Migrator
{
    protected array $properties = [];

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_new(): TableObject {
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
}