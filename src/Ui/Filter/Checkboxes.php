<?php

namespace Osm\Admin\Ui\Filter;

use Osm\Admin\Ui\Filter;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;

#[Type('checkboxes')]
class Checkboxes extends Filter
{
    public string $template = 'ui::filter.checkboxes';

    protected function get_visible(): bool {
        return false;
    }

    public function prepare(): void {
        $this->query->facetCounts($this->property->name);
    }
}