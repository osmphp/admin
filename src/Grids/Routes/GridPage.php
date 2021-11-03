<?php

namespace Osm\Admin\Grids\Routes;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Admin\Grids\Grid;
use Osm\Admin\Queries\Attributes\Of;
use Osm\Admin\Tables\Table;
use Osm\Framework\Http\Route;
use Symfony\Component\HttpFoundation\Response;
use function Osm\view_response;

/**
 * @property Grid $grid
 * @property Table $query
 * @property string $query_class_name
 */
class GridPage extends Route
{
    public function run(): Response
    {
        return view_response('grids::pages.grid', [
            'grid' => $this->grid,
        ]);
    }

    protected function get_grid(): Grid {
        return Grid::new(['query_class_name' => $this->query_class_name]);

    }

    protected function get_query_class_name(): string {
        /* @var Of $of */
        return ($of = $this->__class->attributes[Of::class] ?? null)
            ? $of->class_name
            : throw new Required(__METHOD__);
    }
}