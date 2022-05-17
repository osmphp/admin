<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
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

    public function convert(Query $query): void {
        if (!$this->requiresMigration(static::CONVERT)) {
            return;
        }

        $new = "{$this->migration_class_name}::new";

        /* @var Migration $migration */
        $migration = $new([
            'property' => $this,
            'mode' => static::CONVERT,
            'query' => $query,
        ]);

        $migration->migrate();

        $formula = str_replace('{{old_value}}', $migration->old_value,
            $migration->new_value);
        $query->select("{$formula} AS {$this->new->name}");
    }

    public function requiresMigration(string $mode): bool {
        if (!isset($this->migrations[$mode])) {
            $new = "{$this->migration_class_name}::new";
            $this->migrations[$mode] = $new([
                'property' => $this,
                'mode' => $mode,
            ]);

            $this->migrations[$mode]->migrate();
        }

        return $this->migrations[$mode]->run;
    }

    protected function get_migration_class_name(): string {
        return str_replace('\\Property\\', '\\Migration\\',
            $this->__class->name);
    }

    public function diff(): void {
        $this->rename = $this->old
            && $this->new->name !== $this->old->name
                ? $this->old->name
                : null;

        //throw new NotImplemented($this);
    }
}