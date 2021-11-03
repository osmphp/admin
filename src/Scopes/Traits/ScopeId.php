<?php

namespace Osm\Admin\Scopes\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Admin\Tables\Attributes\Column;

/**
 * @property ?int $scope_id #[
 *      Serialized,
 *      Column\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 * ]
 */
trait ScopeId
{

}