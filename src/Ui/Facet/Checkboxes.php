<?php

namespace Osm\Admin\Ui\Facet;

use Osm\Admin\Ui\Facet;
use Osm\Admin\Ui\Query;
use Osm\Admin\Ui\Query\Facet\Option;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Framework\Search\Hints\Result\Count;
use Osm\Framework\Search\Query as SearchQuery;
use Osm\Framework\Search\Result as SearchResult;

/**
 * Render-time properties:
 *
 */
#[Type('checkboxes')]
class Checkboxes extends Facet
{
    public string $template = 'ui::facet.checkboxes';

    protected function get_visible(): bool {
        return !empty($this->query->result->facets[$this->property->name]);
    }

    public function prepare(): void {
        $this->query->facet($this->property->name);
    }

    public function query(SearchQuery $searchQuery): void
    {
        $searchQuery->facetBy($this->property->name);
    }

    public function populate(Query $query, SearchResult $result): mixed {
        return array_map(
            fn(Count|\stdClass $option) => $this->populateOption($query, $option),
            $result->facets[$this->property->name]->counts);
    }

    protected function populateOption(Query $query, Count|\stdClass $option)
        : Option
    {
        return Option::new([
            'query' => $query,
            'property_name' => $this->property->name,
            'value' => $option->value,
            'count' => $option->count,
            'title' => $this->property->options[$option->value]->title,
        ]);
    }

    protected function get_data(): array {
        return [
            'title' => $this->property->control->title,
            'options' => $this->query->result->facets[$this->property->name],
        ];
    }
}