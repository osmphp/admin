<?php

namespace Osm\Admin\Ui;

use Osm\Framework\Blade\View;

/**
 * Render-time properties:
 *
 * @property ?Facets $facets
 * @property bool $visible
 */
class Sidebar extends View
{
    public string $template = 'ui::sidebar';

    protected function get_visible(): bool {
        return $this->facets?->visible ?: false;
    }

    protected function get_data(): array {
        return [
            'facets' => $this->facets,
        ];
    }
}