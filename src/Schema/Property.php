<?php

namespace Osm\Admin\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Osm\Admin\Schema\Traits\AttributeParser;
use Osm\Admin\Schema\Traits\RequiredSubTypes;
use Osm\Admin\Ui\Control;
use Osm\Admin\Ui\Facet;
use Osm\Admin\Ui\List_;
use Osm\Admin\Ui\Query;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Property as CoreProperty;
use Osm\Core\Attributes\Serialized;
use Osm\Admin\Schema\Index;
use Osm\Framework\Search\Blueprint as SearchBlueprint;
use Osm\Framework\Search\Field;

/**
 * @property Struct $parent
 * @property CoreProperty $reflection
 * @property string $name #[Serialized]
 * @property ?string $rename #[Serialized]
 * @property array $if #[Serialized]
 * @property bool $nullable #[Serialized]
 * @property bool $actually_nullable #[Serialized]
 * @property bool $array #[Serialized]
 * @property bool $explicit #[Serialized]
 * @property ?string $value #[Serialized]
 * @property string[] $formula_if #[Serialized]
 * @property bool $virtual #[Serialized]
 * @property bool $computed #[Serialized]
 * @property bool $overridable #[Serialized]
 * @property ?string $option_class_name #[Serialized]
 * @property bool $index #[Serialized] If set, the property is added to the
 *      search engine index. And it's set if either of `*_index` properties
 *      is set.
 * @property bool $index_filterable #[Serialized] If set,
 *      a filter for the property is created in the search index
 * @property bool $index_faceted #[Serialized]
 * @property bool $index_sortable #[Serialized]
 * @property bool $index_searchable #[Serialized]
 * @property DataType $data_type
 * @property Option $option_handler
 * @property Option[] $options
 * @property bool $faceted #[Serialized] If set, a property is shown in the
 *      faceted navigation in the sidebar
 * @property ?Control $control #[Serialized]
 * @property ?Facet $facet #[Serialized]
 * @property string $title
 * @property string $default_value #[Serialized]
 *
 * Dependencies:
 *
 * @property DataType[] $data_types
 *
 * @uses Serialized
 */
class Property extends Object_
{
    use RequiredSubTypes, AttributeParser;

    public const TINY = 'tiny';
    public const SMALL = 'small';
    public const MEDIUM = 'medium';
    public const LONG = 'long';

    protected function get_parent(): Struct {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): CoreProperty {
        return $this->parent->reflection->properties[$this->name];
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_if(): array {
        return [];
    }

    protected function get_formula_if(): array {
        return [];
    }

    protected function get_nullable(): bool {
        return false;
    }

    protected function get_array(): bool {
        return false;
    }

    protected function get_explicit(): bool {
        return false;
    }

    protected function get_virtual(): bool {
        return false;
    }

    protected function get_computed(): bool {
        return false;
    }

    public function parse(): void {
        $this->parseAttributes();

        $this->array = $this->reflection->array;
        $this->nullable = $this->reflection->nullable;
    }

    public function parseTypeSpecificFormulas(array $types,
        CoreProperty $reflection): void
    {
        $data = $this->parseAttributeData($reflection);

        if (!isset($data->formula)) {
            return;
        }

        $this->formula_if = [];
        foreach ($types as $type) {
            $this->formula_if[$type] = $data->formula;
        }
    }

    public function create(Blueprint $table): void {
        throw new NotImplemented($this);
    }

    public function alter(Blueprint $table, Diff\Property $diff): void {
        throw new NotImplemented($this);
    }

    public function createIndex(SearchBlueprint $index): Field
    {
        throw new NotImplemented($this);
    }

    public function __wakeup(): void {
    }

    protected function get_data_type(): DataType {
        return $this->data_types[$this->type];
    }

    protected function get_data_types(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[Module::class]->data_types;
    }

    protected function get_options(): array {
        return $this->parent->schema->options[$this->option_class_name];
    }

    protected function get_option_handler(): Option {
        return $this->parent->schema->option_handlers[$this->option_class_name];
    }

    protected function get_index(): bool {
        return $this->index_filterable || $this->index_sortable != null ||
            $this->index_searchable != null;
    }

    protected function get_index_filterable(): bool {
        if ($this->faceted) {
            return true;
        }

        if ($this->name == 'id' && !$this->parent->singleton) {
            return true;
        }

        foreach ($this->parent->list_views as $list) {
            if ($list->filterable($this->name)) {
                return true;
            }
        }

        return false;
    }

    protected function get_index_sortable(): bool {
        foreach ($this->parent->list_views as $list) {
            if ($list->sortable($this->name)) {
                return true;
            }
        }

        return false;
    }

    protected function get_index_searchable(): bool {
        return false;
    }

    protected function get_index_faceted(): bool {
        return false;
    }

    protected function get_faceted(): bool {
        return false;
    }

    protected function get_control(): ?Control {
        if ($this->option_class_name) {
            return Control\Select::new([
                'data_type' => $this->data_type,
                'property' => $this,
            ]);
        }

        if (!$this->data_type->default_control) {
            return null;
        }

        $control = clone $this->data_type->default_control;

        $control->property = $this;

        return $control;
    }

    protected function get_facet(): ?Facet {
        if (!$this->control->default_facet) {
            return null;
        }

        $filter = clone $this->control->default_facet;
        $filter->property = $this;

        return $filter;
    }

    public function parseUrlFilter(Query $query, string $operator,
        string|array|bool $value): void
    {
        // by default, properties ignore URL filters
    }

    protected function get_title(): string {
        return Str::title($this->name);
    }

    protected function get_actually_nullable(): bool {
        return $this->nullable || !empty($this->if);
    }

    protected function get_default_value(): string {
        return "NULL";
    }
}