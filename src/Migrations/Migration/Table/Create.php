<?php

namespace Osm\Data\Migrations\Migration\Table;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Migrations\Migration\Table;
use Osm\Data\Schema\Class_;
use Osm\Data\Schema\Property;
use Osm\Data\Scopes\Scope;

/**
 * @property Class_ $class
 */
class Create extends Table
{
    public int $priority = 10;

    protected function get_name(): string {
        return "create_table:{$this->class->table}";
    }

    public function run(): void {
        if (!$this->class->scoped) {
            $this->db->create($this->class->table, function(Blueprint $table) {
                $this->createColumns($table, $this->class);
                $table->json('data')->nullable();
            });
            return;
        }

        if (!$this->scope) {
            $this->db->create($this->class->table, function (Blueprint $table) {
                $this->createPrimaryColumns($table, $this->class);

                $table->unsignedInteger('scope_id')->nullable();
                $table->foreign('scope_id')
                    ->references('id')
                    ->on('scopes')
                    ->onDelete('cascade');
            });
            return;
        }

        $this->db->create("{$this->scope->prefix}{$this->class->table}",
            function(Blueprint $table) {
                $this->createColumns($table, $this->class);
                $table->json('data')->nullable();
            });
    }

    protected function createColumns(Blueprint $table, Class_ $class,
        string $prefix = ''): void
    {
        foreach ($class->properties as $property) {
            if ($property->column) {
                if ($this->scope) {
                    $property->column->createScoped($table, $prefix);
                }
                else {
                    $property->column->create($table, $prefix);
                }
                continue;
            }

            if (isset($class->schema->classes[$property->type])) {
                throw new NotImplemented($this);
            }
        }
    }

    protected function createPrimaryColumns(Blueprint $table, Class_ $class)
        : void
    {
        foreach ($class->properties as $property) {
            $property->column?->createKey($table);
        }
    }

    protected function get_after(): array
    {
        $after = [];

        foreach ($this->planner->migrations as $migration) {
            /* @var static $migration */
            if ($migration::class !== static::class) {
                // only compare with `Create` migrations
                continue;
            }

            if ($this->class->name === $migration->class->name) {
                // skip recursive references
                continue;
            }

            if ($this->class->scoped && $migration->class->name === Scope::class) {
                $after[] = $migration->name;
                continue;
            }

            if ($this->references($this->class, $migration->class)) {
                $after[] = $migration->name;
            }
        }

        return $after;
    }
}