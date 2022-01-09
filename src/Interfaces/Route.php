<?php

namespace Osm\Admin\Interfaces;

use Osm\Admin\Filters\AppliedFilter;
use Osm\Admin\Filters\Hints\AppliedFilters;
use Osm\Admin\Filters\Module as FilterModule;
use Osm\Admin\Forms\Field;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Http\Route as BaseRoute;

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
 * @property string[] $columns
 * @property string $form_url
 * @property array $options
 * @property array $field_options
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

        return $appliedFilters;
    }

    protected function get_objects(): ?array {
        if ($this->object_count > static::MAX_MERGED_OBJECTS) {
            return null;
        }

        return $this->query->get();
    }

    protected function get_object(): \stdClass {
        throw new NotImplemented($this);
    }

    protected function get_columns(): array {
        return [];
    }

    protected function get_form_url(): string {
        throw new NotImplemented($this);
    }

    protected function get_options(): array {
        return [
            'route_name' => $this->route_name,
        ];
    }

    protected function get_field_options(): array {
        $fieldOptions = [];

        foreach ($this->form->fields() as $field) {
            $valueExists = property_exists($this->object, $field->name);
            $options = [
                'initial_value_exists' => $valueExists,
            ];

            if ($valueExists) {
                $options['initial_value'] = $this->object->{$field->name};
            }

            $fieldOptions[$field->name] = $options;
        }

        return $fieldOptions;
    }
}