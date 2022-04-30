<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
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
 */
class Property extends Diff
{
    use RequiredSubTypes;

    public const CREATE = 'create';
    public const PRE_ALTER = 'pre_alter';
    public const POST_ALTER = 'post_alter';

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

    public function migrate(string $mode, Blueprint $table = null): bool {
        throw new NotImplemented($this);
    }

    public function diff(): void {
        $this->rename = $this->old
            && $this->new->name !== $this->old->name
                ? $this->old->name
                : null;

        //throw new NotImplemented($this);
    }

    protected function nullable(string $mode, ?ColumnDefinition $column): bool {
        $changed = $mode === static::CREATE ||
            $this->old->actually_nullable != $this->new->actually_nullable;

        // defer conversion from nullable to non-nullable from pre-alter
        // to post-alter phase
        $deferred = $mode !== static::CREATE &&
            $this->old->actually_nullable &&
            !$this->new->actually_nullable;

        $column?->nullable($deferred
            ? $mode === static::PRE_ALTER
            : $this->new->actually_nullable);

        return match($mode) {
            static::CREATE => true,
            static::PRE_ALTER => $changed && !$deferred,
            static::POST_ALTER => $changed && $deferred,
        };
    }

    protected function change(string $mode, ?ColumnDefinition $column): void {
        if ($mode !== static::CREATE) {
            $column?->change();
        }
    }
}