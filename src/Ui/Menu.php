<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Samples\Products\Product;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use function Osm\ui_query;
use Osm\Core\Attributes\Serialized;

/**
 * @property bool $visible
 * @property MenuItem[] $items #[Serialized]
 *
 * @uses Serialized
 */
class Menu extends View
{
    public string $template = 'ui::menu';

    protected function get_visible(): bool {
        return true;
    }

    protected function get_items(): array {
        throw new Required(__METHOD__);
    }

    protected function get_data(): array {
        return [
            'items' => $this->items,
        ];
    }
}