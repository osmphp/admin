<?php

namespace Osm\Admin\Ui;

use Osm\Framework\Blade\View as BaseView;

/**
 * Render-time properties:
 *
 * @property ?Filters $filters
 * @property bool $visible
 */
class Sidebar extends BaseView
{
    public string $template = 'ui::sidebar';

    protected function get_visible(): bool {
        return $this->filters?->visible ?: false;
    }

    protected function get_data(): array {
        return [
            'filters' => $this->filters,
        ];
    }
}