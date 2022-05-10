<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Monolog\Logger;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Table $table
 * @property \stdClass|PropertyObject|null $old
 * @property PropertyObject $new
 * @property ?string $rename
 * @property string $non_null_formula
 * @property Logger $log
 * @property string $migration_class_name
 */
class Property extends Diff
{
    use RequiredSubTypes;

    public const CREATE = 'create';
    public const PRE_ALTER = 'pre_alter';
    public const POST_ALTER = 'post_alter';

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

    public function migrate(string $mode, Blueprint $table): void {
        if (!$this->requiresMigration($mode)) {
            return;
        }

        $new = "{$this->migration_class_name}::new";

        $new([
            'property' => $this,
            'mode' => $mode,
            'table' => $table,
        ])->migrate();
    }

    public function requiresMigration(string $mode): bool {
        if (!isset($this->migrations[$mode])) {
            $new = "{$this->migration_class_name}::new";
            $this->migrations[$mode] = $new([
                'property' => $this,
                'mode' => $mode,
            ]);
        }

        return $this->migrations[$mode]->migrate();
    }

    protected function get_migration_class_name(): string {
        return str_replace('\\Property\\', '\\Migration\\',
            $this->__class->name);
    }

    public function convert(Query $query = null): bool {
        throw new NotImplemented($this);
    }

    public function diff(): void {
        $this->rename = $this->old
            && $this->new->name !== $this->old->name
                ? $this->old->name
                : null;

        //throw new NotImplemented($this);
    }

    protected function type(string $mode, ?Blueprint $table): bool
    {
        if ($mode == static::CREATE) {
            return true;
        }

        if ($this->old->type === $this->new->type) {
            return false;
        }

        $this->fromType($mode, $table);

        return true;
    }

    protected function fromType(string $mode, ?Blueprint $table): bool {
        // by default, trust MySql to do all the data conversion
        // implicitly during DDL type change
        return false;
    }

    protected function nullable(string $mode, ?ColumnDefinition $column): bool {
        $changed = $mode === static::CREATE ||
            $this->old->actually_nullable != $this->new->actually_nullable;

        // defer conversion from nullable to non-nullable from pre-alter
        // to post-alter phase
        $makeNonNull = $mode !== static::CREATE &&
            $this->old->actually_nullable &&
            !$this->new->actually_nullable;

        $column?->nullable($makeNonNull
            ? $mode === static::PRE_ALTER
            : $this->new->actually_nullable);

        return match($mode) {
            static::CREATE => true,
            static::PRE_ALTER => $changed && !$makeNonNull,
            static::POST_ALTER => $changed && $makeNonNull,
        };
    }

    protected function change(string $mode, ?ColumnDefinition $column): void {
        if ($mode !== static::CREATE) {
            $column?->change();
        }
    }

    protected function convertToNonNull(string $formula): string {
        if (!$this->old) {
            return $formula;
        }

        $makeNonNull = $this->old->actually_nullable &&
            !$this->new->actually_nullable;

        return $makeNonNull
            ? "{$formula} ?? {$this->non_null_formula}"
            : $formula;
    }

    protected function get_non_null_formula(): string {
        throw new NotImplemented($this);
    }

    protected function log(string $message): void {
        $this->log->notice($message);
    }

    protected function get_log(): Logger {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->logs->migrations;
    }
}