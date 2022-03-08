<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Schema\Struct;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View as BaseView;
use function Osm\theme_specific;

/**
 * Render-time properties:
 *
 * @property Query $query
 * @property Facet[] $facets
 * @property Struct $struct
 * @property bool $visible
 */
class Facets extends BaseView
{
    public string $template = 'ui::facets';

    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }

    protected function get_struct(): Struct {
        throw new Required(__METHOD__);
    }

    protected function get_facets(): array {
        $facets = [];

        foreach ($this->struct->properties as $property) {
            if (!$property->faceted) {
                continue;
            }

            if (!$property->facet) {
                continue;
            }

            $facets[] = theme_specific($property->facet, [
                'query' => $this->query,
            ]);
        }

        return $facets;
    }

    protected function get_visible(): bool {
        foreach ($this->facets as $facet) {
            if ($facet->visible) {
                return true;
            }
        }

        return false;
    }

    protected function get_data(): array {
        return [
            'facets' => $this->facets,
        ];
    }

    public function prepare(): void {
        foreach ($this->facets as $facet) {
            $facet->prepare();
        }
    }
}