<?php

namespace Osm\Admin\Schema\Property;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

/**
 * @property string $on_delete #[Serialized]
 *
 * @uses Serialized
 */
#[Type('record')]
class Record extends Bag
{
    public const ON_DELETE_SET_NULL = 'set null';
    public const ON_DELETE_CASCADE = 'cascade';
    public const ON_DELETE_RESTRICT = 'restrict';

    public string $refs_name = Table::SCHEMA_PROPERTY;
    public string $refs_root_class_name = Table::ROOT_CLASS_NAME;

    protected function get_on_delete(): string {
        return static::ON_DELETE_SET_NULL;
    }

    public function create(Blueprint $table): void {
        $id = $this->ref->properties['id'];
        $name = "{$this->name}_id";

        $column = match ($id->size) {
            static::TINY => $table->tinyInteger($name),
            static::SMALL => $table->smallInteger($name),
            static::MEDIUM => $table->integer($name),
            static::LONG => $table->bigInteger($name),
        };

        if ($id->unsigned) {
            $column->unsigned();
        }

        if ($this->nullable || !empty($this->if)) {
            $column->nullable();
        }

        $table->foreign($name)
            ->references($id->name)
            ->on($this->ref->table_name)
            ->onDelete($this->on_delete);
    }
}