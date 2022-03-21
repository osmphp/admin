<?php

namespace Osm\Admin\Ui\Grid;

use Illuminate\Support\Str;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\List_\Grid;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Framework\Blade\Attributes\RenderTime;

/**
 * @property Control $control
 * @property string $cell_template #[RenderTime]
 * @property Grid $grid #[RenderTime]
 * @property string $name #[RenderTime]
 * @property string $title #[RenderTime]
 *
 * @uses RenderTime
 */
class Column extends View
{
    use RequiredSubTypes;

    protected function get_control(): Control {
        throw new Required(__METHOD__);
    }

    protected function get_cell_template(): string {
        throw new Required(__METHOD__);
    }

    protected function get_grid(): Grid {
        throw new Required(__METHOD__);
    }

    protected function get_data(): array {
        return [
            'column' => $this,
        ];
    }

    public function data(\stdClass $item): array {
        return [
            'column' => $this,
            'item' => $item,
        ];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        return Str::title($this->name);
    }

    public function display(\stdClass $item): ?string {
        return $item->{$this->name} ?? null;
    }
}