<?php

namespace Osm\Admin\Ui;

use Osm\Framework\Blade\View as BaseView;

/**
 * Render-time properties:
 *
 * @property Query $query
 * @property bool $visible
 */
class Filters extends BaseView
{
    public string $template = 'ui::filters';

    protected function get_visible(): bool {
        return true;
    }

    protected function get_data(): array {
        return [
        ];
    }
}