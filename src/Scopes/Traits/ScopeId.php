<?php

namespace Osm\Data\Scopes\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes\Column;

/**
 * @property ?int $scope_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 * ]
 */
trait ScopeId
{

}