<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Filters\AppliedFilter;
use Osm\Admin\Filters\Hints\AppliedFilters;
use Osm\Admin\Filters\Module as FilterModule;
use Osm\Admin\Forms\Field;
use Osm\Admin\Interfaces\Exceptions\FilterExpected;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Http\Route as BaseRoute;
use function Osm\__;

/**
 * @property string $class_name
 * @property string $interface_type
 * @property string $route_name
 * @property Class_ $class
 * @property Interface_ $interface
 * @property int $object_count Number of retrieved objects
 * @property Query $query The object query with applied filters and
 *      search criteria
 * @property FilterModule $filter_module
 * @property AppliedFilters[] $applied_filters
 * @property \stdClass[]|null $objects
 * @property \stdClass $object
 * @property array $multiple
 * @property string[] $columns
 * @property string $form_url
 * @property array $options
 * @property array $field_options
 * @property bool $can_show_all
 * @property string $grid_url
 */
class Route extends BaseRoute
{
    public const MAX_MERGED_OBJECTS = 10;

    protected function get_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_interface_type(): string {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->schema->classes[$this->class_name];
    }

    protected function get_interface(): Interface_ {
        return $this->class->interfaces[$this->interface_type];
    }

    protected function get_object_count(): int {
        return $this->query->count();
    }

    protected function get_query(): Query {
        $query = $this->class->storage->query();

        $this->applyFilters($query);

        $query->select(...$this->columns);

        return $query;
    }

    protected function applyFilters(Query $query): void {
        foreach ($this->applied_filters as $propertyName => $appliedFilters) {
            $this->class->filters[$propertyName]->apply($query,
                $appliedFilters->operator, $appliedFilters->values);
        }
    }

    protected function get_filter_module(): Module|BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[FilterModule::class];
    }

    protected function get_applied_filters(): array {
        $appliedFilters = [];

        foreach ($this->http->query as $key => $value) {
            foreach ($this->filter_module->operators as $url => $operator) {
                if ($url) {
                    if (!str_ends_with($key, $url)) {
                        continue;
                    }

                    $propertyName = mb_substr($key, 0,
                        mb_strlen($key) - mb_strlen($url));
                }
                else {
                    $propertyName = $key;
                }

                if ($this->can_show_all && $propertyName === 'id') {
                    continue;
                }

                if (!($filter = $this->class->filters[$propertyName] ?? null)) {
                    continue;
                }

                if (!in_array($operator, $filter->supports)) {
                    continue;
                }

                if (empty($values = $filter->parse($operator, $value))) {
                    continue;
                }

                $appliedFilters[$propertyName] = (object)[
                    'operator' => $operator,
                    'values' => $values,
                ];

                break;
            }
        }

        if (empty($appliedFilters)
            && !$this->can_show_all
            && ($this->http->query['all'] ?? null) !== true)
        {
            throw new FilterExpected(__("Filter(s) expected"));
        }

        return $appliedFilters;
    }

    protected function get_objects(): ?array {
        return $this->query->get();
    }

    protected function get_object(): \stdClass {
        throw new NotImplemented($this);
    }

    protected function get_multiple(): array {
        return [];
    }

    protected function get_columns(): array {
        return [];
    }

    protected function get_form_url(): string {
        throw new NotImplemented($this);
    }

    protected function get_options(): array {
        throw new NotImplemented($this);
    }

    protected function get_field_options(): array {
        $fieldOptions = [];

        foreach ($this->form->fields() as $field) {
            $fieldOptions[$field->name] = [
                'value' => $this->object->{$field->name} ?? null,
                'multiple' => $this->multiple[$field->name] ?? false,
            ];

            if ($fieldOptions[$field->name]['multiple']) {
                $fieldOptions[$field->name]['s_multiple_values'] =
                    __("<multiple values>");
                $fieldOptions[$field->name]['s_empty'] =
                    __("<empty>");
            }
        }

        return $fieldOptions;
    }


    protected function get_grid_url(): string {
        $url = $this->interface->url('GET /');

        $appliedFilters = $this->applied_filters;
        unset($appliedFilters['id']);

        return $this->interface->filterUrl($url, $appliedFilters);
    }
}