<?php

namespace Osm\Data\Scopes;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes\Table;
use Osm\Data\Tables\Attributes\Column;
use Osm\Data\Tables\Traits\Id;

/**
 * @property ?int $parent_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 * ]
 * @property string $prefix
 */
#[Table('scopes')]
class Scope extends Object_
{
    use Id;

    protected function get_prefix(): string {
        $id = $this->id ?? 0;

        return "s{$id}__";
    }
}