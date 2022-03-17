<?php

namespace Osm\Admin\Ui\Query\Facet;

use Osm\Admin\Ui\Hints\UrlAction;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
/**
 * Serialized properties are returned through the API.
 *
 * @property mixed $value #[Serialized]
 * @property int $count #[Serialized]
 * @property string $title #[Serialized]
 * @property bool $applied #[Serialized]
 * @property string $action_url #[Serialized]
 * @property array $actions
 * @property Query $query
 * @property string $property_name
 * @property string $url
 *
 * @uses Serialized
 */
class Option extends Object_
{
    protected function get_query(): Query {
        throw new Required(__METHOD__);
    }

    protected function get_value(): string {
        throw new Required(__METHOD__);
    }

    protected function get_count(): int {
        throw new Required(__METHOD__);
    }

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_applied(): bool {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        return $this->query->toUrl('GET /', $this->actions);
    }

    protected function get_actions(): array {
        return $this->applied
            ? [
                UrlAction::removeOption($this->property_name, $this->value),
            ]
            : [
                UrlAction::addOption($this->property_name, $this->value),
            ];
    }

    protected function get_action_url(): string {
        return UrlAction::toString($this->actions);
    }
}