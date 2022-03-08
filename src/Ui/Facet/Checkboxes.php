<?php

namespace Osm\Admin\Ui\Facet;

use Osm\Admin\Ui\Facet;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('checkboxes')]
class Checkboxes extends Facet
{
    public string $template = 'ui::filter.checkboxes';

    protected function get_visible(): bool {
        return false;
    }

    public function prepare(): void {
        $this->query->facet($this->property->name);
    }
}