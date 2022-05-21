<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Table $table
 * @property \stdClass|PropertyObject|null $old
 * @property PropertyObject $new
 * @property ?string $rename
 * @property string $migration_class_name
 */
class Property extends Diff
{
    use RequiredSubTypes;

    public const CREATE = 'create';
    public const PRE_ALTER = 'pre_alter';
    public const CONVERT = 'convert';
    public const POST_ALTER = 'post_alter';

    protected ?string $attribute_name = null;
    protected bool $create_column = false;
    protected bool $drop_column = false;
    protected bool $create_json = false;
    protected bool $drop_json = false;
    /**
     * @var callable[]
     */
    protected array $convert = [];
    /**
     * @var callable[]
     */
    protected array $column = [];

    /**
     * @var Migration[]
     */
    protected array $migrations = [];

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_new(): PropertyObject {
        throw new Required(__METHOD__);
    }

    protected function get_alter(): bool {
        throw new Required(__METHOD__);
    }

    protected function get_rename(): ?string {
        throw new Required(__METHOD__);
    }

    protected function get_migration_class_name(): string {
        return str_replace('\\Property\\', '\\Migration\\',
            $this->__class->name);
    }

    public function diff(): void {
//        $this->rename = $this->old
//            && $this->new->name !== $this->old->name
//                ? $this->old->name
//                : null;

        throw new NotImplemented($this);
    }

    protected function attribute(string $attributeName, callable $callback)
        : void
    {
        $this->attribute_name = $attributeName;

        try {
            $callback();
        }
        finally {
            $this->attribute_name = null;
        }
    }

    protected function explicit(): void {
        $this->attribute('explicit', function() {
            if ($this->new->explicit) {
                if ($this->old) {
                    if (!$this->old->explicit) {
                        $this->createColumn();
                        $this->convert();
                        $this->dropJson();
                    }
                }
                else {
                    $this->createColumn();
                }

            }
            else { // !$this->new->explicit
                if ($this->old?->explicit) {
                    $this->createJson();
                    $this->convert();
                    $this->dropColumn();
                }
            }
        });
    }

    protected function type(): void {
        $this->attribute('type', function() {
        });
    }

    protected function nullable(): void {
        $this->attribute('nullable', function() {
        });
    }

    /**
     * Forces data conversion of the property, and `$callback`, if provided,
     * modifies the data conversion formula.
     *
     * @param ?callable $callback
     */
    protected function convert(callable $callback = null): void {
        $this->convert[$this->attribute_name] = $callback;
    }

    protected function createColumn(): void {
        $this->create_column = true;
    }

    protected function dropColumn(): void {
        $this->drop_column = true;
    }

    protected function createJson(): void {
        $this->create_json = true;
    }

    protected function dropJson(): void {
        $this->drop_json = true;
    }

    protected function define(Blueprint $table): ColumnDefinition {
        throw new NotImplemented($this);
    }

    public function migrate(string $mode, Blueprint $table = null,
        Query $query = null): bool
    {
        return match ($mode) {
            static::CREATE =>
                $this->migrateWithoutData($table),
            static::PRE_ALTER => empty($this->convert)
                ? $this->migrateWithoutData($table)
                : $this->beforeMigratingData($table),
            static::CONVERT =>
                !empty($this->convert) && $this->migrateData($query),
            static::POST_ALTER =>
                !empty($this->convert) && $this->afterMigratingData($table),
        };
    }

    protected function migrateWithoutData(?Blueprint $table): bool {
        $run = false;
        if ($this->new->explicit) {
            $column = $table ? $this->define($table): null;

            if ($this->create_column) {
                $run = true;
            }
            else {
                $column?->change();
            }

            foreach ($this->column as $callback) {
                $run = $run || $callback($column, $table);
            }
        }
        return $run;
    }

    protected function beforeMigratingData(?Blueprint $table): bool {
        throw new NotImplemented($this);
    }

    protected function migrateData(Query $query = null): bool {
        throw new NotImplemented($this);
    }

    protected function afterMigratingData(?Blueprint $table): bool {
        throw new NotImplemented($this);
    }
}