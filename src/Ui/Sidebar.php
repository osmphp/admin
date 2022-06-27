<?php

namespace Osm\Admin\Ui;

use Osm\Core\App;
use Osm\Framework\Blade\View;
use function Osm\view;

/**
 * Render-time properties:
 *
 * @property Menu $menu
 * @property ?Facets $facets
 * @property bool $visible
 */
class Sidebar extends View
{
    public string $template = 'ui::sidebar';

    protected function get_visible(): bool {
        return $this->facets?->visible ||
            $this->menu->visible;
    }

    protected function get_data(): array {
        return [
            'facets' => $this->facets,
            'menu' => $this->menu,
        ];
    }

    protected function get_menu(): Menu|View {
        global $osm_app; /* @var App $osm_app */

        return view($osm_app->schema->menu);
    }
}