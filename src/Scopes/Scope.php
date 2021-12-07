<?php

namespace Osm\Admin\Scopes;

use Osm\Admin\Base\Attributes\Form;
use Osm\Admin\Base\Attributes\Grid;
use Osm\Admin\Base\Attributes\Icon;
use Osm\Admin\Tables\TableQuery;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Table;
use Osm\Admin\Scopes\Attributes\Storage;
use Osm\Admin\Base\Traits\Id;

/**
 * @property ?int $parent_id #[
 *      Serialized,
 *      Table\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 *      Form\Int_(10, 'Parent Scope'),
 * ]
 * @property ?int $level #[
 *      Serialized,
 *      Table\Int_(unsigned: true),
 * ]
 * @property ?string $id_path #[
 *      Serialized,
 *      Table\String_,
 * ]
 * @property ?string $title #[
 *      Serialized,
 *      Grid\String_('Title'),
 *      Form\String_(20, 'Title'),
 * ]
 * @property string $prefix
 * @property Scope $parent #[Serialized(not_having: 'children')]
 * @property Scope[] $children #[Serialized]
 */
#[
    Storage\Scopes,
    Icon('/scopes/', 'Scopes'),
    Grid('/scopes/', 'Scopes', select: ['id', 'title']),
    Form\Create('/scopes/create', 'New Scope'),
    Form\Edit('/scopes/edit', ":title - Scope"),
]
class Scope extends Object_
{
    use Id;

    protected function get_prefix(): string {
        $id = $this->id ?? 0;

        return "s{$id}__";
    }

    public function join_parent(TableQuery $query, string $joinMethod,
        string $from, string $as): void
    {
        $query->db_query->$joinMethod("scopes AS {$as}",
            "{$from}.parent_id", '=', "{$as}.id");
    }
}