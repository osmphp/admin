<?php

namespace Osm\Admin\Schema\Diff;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Schema\Diff;
use Osm\Admin\Schema\Property as PropertyObject;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;

/**
 * @property Table $table
 * @property \stdClass|PropertyObject|null $old
 * @property PropertyObject $new
 * @property bool $alter
 * @property ?string $rename
 */
class Property extends Diff
{
    use RequiredSubTypes;

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

    public function migrate(Blueprint $table): void {
        if (!$this->new->explicit) {
            return;
        }

        if ($this->alter) {
            $this->alter($table);
        }
        else {
            $this->create($table);
        }
    }

    protected function create(Blueprint $table): void {
        throw new NotImplemented($this);
    }

    protected function alter(Blueprint $table): void {
        throw new NotImplemented($this);
    }

    public function diff(): void {
        $this->alter = $this->old != null;
        $this->rename = $this->old
            && $this->new->name !== $this->old->name
                ? $this->old->name
                : null;

        //throw new NotImplemented($this);
    }
}