<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Traits\LogsMigrations;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use function Osm\__;

/**
 * @property Property $property Property diff to be migrated
 * @property string $mode Migration stage
 * @property ?Blueprint $table DDL query to add property definition into
 * @property ?Query $query SQL query to add property conversion formula into
 * @property ?ColumnDefinition $column New DDL definition of the property
 * @property ?string $old_value SQL formula that extracts the current
 *      property value
 * @property ?string $new_value SQL formula that converts current property value
 *      as needed by its new definition
 * @property string $default_value SQL formula that returns the default value
 *      for this property
 */
class Migration extends Object_
{
    use LogsMigrations;

    public bool $run = false;
    public bool $rename_old_column = false;

    protected function get_property(): Property {
        throw new Required(__METHOD__);
    }

    protected function get_mode(): string {
        throw new Required(__METHOD__);
    }

    public function migrate(): void {
        throw new NotImplemented($this);
    }

    protected function column(): ColumnDefinition {
        throw new NotImplemented($this);
    }

    protected function value(string $name = null): string {
        $old = $this->property->old;
        $name = $name ?? $old->name;
        return $old->explicit
            ? "COLUMN('{$name}', '{$old->type}')"
            : "DATA('{$name}', '{$old->type}')";
    }

    protected function init(): void {
        switch ($this->mode) {
            case Property::CREATE:
            case Property::PRE_ALTER:
            case Property::POST_ALTER:
                if ($this->table) {
                    if ($this->property->old) {
                        $this->logProperty(__("Altering property ':property'", [
                            'property' => $this->property->new->name,
                        ]));
                    }
                    else {
                        $this->logProperty(__("Creating property ':property'", [
                            'property' => $this->property->new->name,
                        ]));
                    }
                }

                // in a DDL migration, prepare a column definition
                // that *may* be used in actual DDL statement if other
                // methods of this class report that such a migration is needed.
                if ($this->table && $this->property->new->explicit) {
                    $this->column = $this->column();

                    if ($this->property->old?->explicit) {
                        // if the property column already exists in
                        // the database change the existing column
                        // instead of creating the
                        // new one
                        $this->column->change();
                    }
                }
                break;
            case Property::CONVERT:
                // in value migration SQL, prepare initial conversion formula
                if ($this->query && $this->property->old) {
                    $this->old_value = $this->value();

                    // the `{{old_value}}` placeholder will be replaced
                    // before running the actual query
                    $this->new_value = "{{old_value}}";
                }
                break;
        }
    }

    protected function explicit(): void {
        if ($this->table) {
            $this->logAttribute('explicit');
        }

        if ($this->property->new->explicit) {
            if (!$this->property->old?->explicit) {
                $this->becomeExplicit();
            }
        }
        else {
            if ($this->property->old?->explicit) {
                $this->becomeImplicit();
            }
        }
    }

    /**
     * If a property becomes explicit, a column is created. If the property
     * already exists, its data is converted using default conversion formula
     */
    protected function becomeExplicit(): void {
        switch ($this->mode) {
            case Property::CREATE:
            case Property::PRE_ALTER:
                $this->run('explicit');
                break;
            case Property::CONVERT:
                if ($this->property->old) {
                    $this->run('explicit');
                }
                break;
            case Property::POST_ALTER:
                break;
        }
    }

    /**
     * If the property stops being explicit, its column is dropped. It may
     * only happen on ALTER TABLE, after the data conversion.
     */
    protected function becomeImplicit(): void {
        switch ($this->mode) {
            case Property::CREATE:
            case Property::PRE_ALTER:
                break;
            case Property::CONVERT:
                if ($this->property->old) {
                    $this->run('explicit');
                }
                break;
            case Property::POST_ALTER:
                $this->run('explicit');
                if ($this->table) {
                    $this->logAttribute('explicit');
                    $this->table->dropColumn($this->property->old->name);
                }
                break;
        }
    }

    protected function type(): void {
        if ($this->table) {
            $this->logAttribute('type');
        }

        if (!$this->property->old ||
            $this->property->old->type === $this->property->new->type)
        {
            return;
        }

        if ($this->property->new->explicit && $this->property->old->explicit &&
            $this->changeTypeByDbMeans())
        {
            $this->runCreateOrPreAlterMigration('type');
        }
        else {
            $this->renameOldColumn('type');
            $this->convertType();
        }
    }

    protected function runCreateOrPreAlterMigration(string $attr): void {
        if ($this->mode == Property::CREATE ||
            $this->mode == Property::PRE_ALTER)
        {
            $this->run($attr);
        }
    }

    protected function renameOldColumn(string $attr): void {
        if (!$this->property->new->explicit || !$this->property->old->explicit) {
            return;
        }

        if ($this->rename_old_column) {
            return;
        }

        $this->rename_old_column = true;

        switch ($this->mode) {
            case Property::CREATE:
                $this->cantAlterPropertyOnCreate();
            case Property::PRE_ALTER:
                if ($this->table) {
                    $this->table->renameColumn($this->property->old->name,
                        "old__{$this->property->old->name}");
                }
                $this->run($attr);
                break;
            case Property::CONVERT:
                $this->old_value = $this->value(
                    "old__{$this->property->old->name}");
                $this->run($attr);
                break;
            case Property::POST_ALTER:
                if ($this->table) {
                    $this->table->dropColumn(
                        "old__{$this->property->old->name}");
                }
                break;
        }
    }

    protected function changeTypeByDbMeans(): bool {
        throw new NotImplemented($this);
    }

    protected function cantAlterPropertyOnCreate(): void {
        throw new NotSupported(__("Can't alter property ':property' in non-existent table ':table'", [
            'property' => $this->property->new->name,
            'table' => $this->property->new->parent->table_name,
        ]));
    }

    protected function convertType(): void {
        if ($this->mode == Property::CONVERT) {
            $this->new_value = "CONVERT({$this->new_value}, " .
                "'{$this->property->old->type}', '" .
                "{$this->property->new->type}', $this->default_value)";
        }
    }

    protected function get_default_value(): string {
        throw new NotImplemented($this);
    }

    protected function nullable(): void {
        if ($this->table) {
            $this->logAttribute('nullable');
        }

        if (!$this->property->new->actually_nullable &&
            $this->property->old?->actually_nullable)
        {
            $this->becomeNonNullable();
        }
        elseif ($this->mode == Property::CREATE ||
                $this->mode == Property::PRE_ALTER)
        {
            if ($this->column) {
                $this->column->nullable($this->property->new->actually_nullable);
            }

            if ($this->property->new->actually_nullable !==
                $this->property->old?->actually_nullable)
            {
                $this->run('nullable');
                // test
            }
        }
    }

    protected function becomeNonNullable(): void {
        switch ($this->mode) {
            case Property::CREATE:
            case Property::PRE_ALTER:
                break;
            case Property::CONVERT:
                $this->new_value = "{$this->new_value} ?? $this->default_value";
                break;
            case Property::POST_ALTER:
                if ($this->column) {
                    $this->column->nullable(false);
                }
                $this->run('nullable');
                break;
        }
    }

    protected function logAttribute(string $attr): void {
        $old = var_export($this->property->old->$attr ?? null,
            true);
        $new = var_export($this->property->new->$attr, true);

        if ($this->property->old) {
            if ($old === $new) {
                return;
            }

            $message = __(":attribute: :old => :new", [
                'attribute' => $attr,
                'old' => $old,
                'new' => $new,
            ]);
        }
        else {
            $message = __(":attribute: :new", [
                'attribute' => $attr,
                'new' => $new,
            ]);
        }

        $this->log->notice('        ' . $message);
    }

    protected function run(string $attr): void {
        $this->run = true;

        if ($this->table && $this->property->old) {
            $this->log->notice("        !{$attr}");
        }
    }
}