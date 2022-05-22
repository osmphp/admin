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

    /**
     * During `diff()`, specifies the attribute currently being processed.
     *
     * @var string|null
     */
    protected ?string $attribute_name = null;

    /**
     * Specifies whether an explicit column for this property should be created
     *
     * @var bool[]
     */
    protected array $create_column = [];

    /**
     * Specifies whether the explicit column should be dropped
     *
     * @var bool[]
     */
    protected array $drop_column = [];

    /**
     * Specifies whether an implicit property storage should be created and
     * initialized in the `_data` column.
     *
     * @var bool[]
     */
    protected array $create_json = [];

    /**
     * Specifies whether an implicit property storage should be removed from the
     * `_data` column.
     *
     * @var bool[]
     */
    protected array $drop_json = [];

    /**
     * Specifies whether existing explicit column should be renamed to `old__x`
     * before data conversion, and dropped afterwards.
     *
     * @var bool[]
     */
    protected array $rename_old_column = [];

    /**
     * Specifies whether property (be it explicit or not) definition has changed.
     *
     * @var bool[]
     */
    protected array $change = [];

    /**
     * Specifies whether data conversion is required, and if so,
     * data conversion formula
     *
     * @var callable[]|bool[]
     */
    protected array $convert = [];

    /**
     * If an explicit column should be created or altered, specified the
     * exact definition
     *
     * @var callable[]|bool[]
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

    protected function define(Blueprint $table): ColumnDefinition {
        throw new NotImplemented($this);
    }

    protected function letDbToConvertData(): bool {
        return false;
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
            $changed = $this->change($this->old?->type !== $this->new->type);

            // This method adds nothing to the column definition. The
            // default DB type is already specified after calling the
            // `define()` method, and it may be adjusted later when
            // diffing `size` and `length` attributes.

            if (!$changed || !$this->old) {
                return;
            }

            if ($this->new->explicit && $this->old->explicit &&
                $this->letDbToConvertData())
            {
                return;
            }

            if ($this->old->explicit) {
                $this->renameOldColumn();
            }

            $this->convert();
        });
    }

    protected function nullable(): void {
        $this->attribute('nullable', function() {
            $changed = $this->change(!$this->old ||
                $this->old->actually_nullable !== $this->new->actually_nullable);

            if ($this->new->explicit) {
                $this->column(fn(?ColumnDefinition $column) =>
                    $column?->nullable($this->new->actually_nullable)
                );
            }

            if (!$this->new->actually_nullable && $this->old?->actually_nullable) {
                $this->convert(fn(string $value) =>
                    "{$value} ?? {$this->new->default_value}");
            }
        });
    }

    /**
     * Forces data conversion of the property, and `$callback`, if provided,
     * modifies the data conversion formula.
     *
     * @param ?callable $callback
     */
    protected function convert(callable $callback = null): void {
        $this->convert[$this->attribute_name] = $callback ?? true;
    }

    protected function column(callable $callback = null): void {
        $this->column[$this->attribute_name] = $callback ?? true;
    }

    protected function createColumn(): void {
        $this->create_column[$this->attribute_name] = true;
    }

    protected function dropColumn(): void {
        $this->drop_column[$this->attribute_name] = true;
    }

    protected function createJson(): void {
        $this->create_json[$this->attribute_name] = true;
    }

    protected function dropJson(): void {
        $this->drop_json[$this->attribute_name] = true;
    }

    protected function change(bool $change): bool {
        if ($change) {
            $this->change[$this->attribute_name] = true;
        }

        return $change;
    }

    protected function renameOldColumn(): void {
        $this->rename_old_column[$this->attribute_name] = true;
    }

    public function migrate(string $mode, Blueprint $table = null,
        Query $query = null): bool
    {
        return match ($mode) {
            static::CREATE =>
                $this->migrateWithoutData($table),
            static::PRE_ALTER => count($this->convert)
                ? $this->beforeMigratingData($table)
                : $this->migrateWithoutData($table),
            static::CONVERT =>
                count($this->convert) && $this->migrateData($query),
            static::POST_ALTER =>
                count($this->convert) && $this->afterMigratingData($table),
        };
    }

    protected function migrateWithoutData(?Blueprint $table): bool {
        return $this->migrateColumn($table);
    }

    protected function beforeMigratingData(?Blueprint $table): bool {
        $run = false;

        if ($this->rename_old_column) {
            $this->migrateColumn($table);
            $table?->renameColumn($this->old->name, "old__{$this->old->name}");
            $run = true;
        }

        return $run;
    }

    protected function migrateData(Query $query = null): bool {
        if (!count($this->convert)) {
            return false;
        }

        $propertyName = $this->rename_old_column
            ? "old__{$this->old->name}":
            $this->old->name;

        $value = $this->old->explicit
            ? "COLUMN('{$propertyName}', '{$this->old->type}')"
            : "DATA('{$propertyName}', '{$this->old->type}')";

        foreach ($this->convert as $callback) {
            if ($callback === true) {
                continue;
            }

            $value = $callback($value);
        }

        $query?->select("{$value} AS {$this->new->name}");
        return true;
    }

    protected function afterMigratingData(?Blueprint $table): bool {
        if ($this->rename_old_column) {
            $table?->dropColumn("old__{$this->old->name}");
            $run = true;
        }
        else {
            $run = $this->migrateColumn($table);
        }

        return $run;
    }

    protected function migrateColumn(?Blueprint $table): bool {
        $run = false;

        if ($this->new->explicit) {
            $column = $table ? $this->define($table): null;

            if ($this->create_column) {
                $run = true;
            }
            else {
                $column?->change();
                $run = count($this->change) > 0;
            }

            foreach ($this->column as $callback) {
                $callback($column, $table);
            }
        }
        return $run;
    }
}