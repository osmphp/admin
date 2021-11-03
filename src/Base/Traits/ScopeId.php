<?php

namespace Osm\Admin\Base\Traits;

use Osm\Core\Attributes\Serialized;
use Osm\Admin\Base\Attributes\Table;

/**
 * @property ?int $scope_id #[
 *      Serialized,
 *      Table\Int_(unsigned: true, references: 'scopes.id', on_delete: 'cascade'),
 * ]
 */
trait ScopeId
{

}