<?php

namespace Osm\Admin\Indexing;

use Osm\Admin\Base\Attributes\Indexer\Source;
use Osm\Admin\Base\Attributes\Indexer\Target;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Schema;
use Osm\Core\App;
use Osm\Core\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\__;
use function Osm\sort_by_dependency;

/**
 * @property Schema $schema
 * @property string $name #[Serialized]
 * @property Class_ $reflection
 * @property string[] $sources #[Serialized]
 * @property string $target #[Serialized]
 * @property ?string $target_type #[Serialized]
 * @property Indexer $indexer
 * @property Property[] $properties #[Serialized]
 */
class Index extends Object_
{
    public function inserting(Query $query, \stdClass $data): void
    {
        $this->indexer->inserting($query, $data);
    }

    public function inserted(Query $query, \stdClass $data, &$modified): void
    {
        $this->indexer->inserted($query, $data, $modified);
    }

    public function updating(Query $query, \stdClass $data): void
    {
        $this->indexer->updating($query, $data);
    }

    protected function get_schema(): Schema {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_reflection(): Class_ {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->classes[$this->name];
    }

    protected function get_sources(): array {
        /* @var Source[] $attributes */
        $attributes = $this->reflection->attributes[Source::class] ?? [];
        $sources = [];

        foreach ($attributes as $attribute) {
            $sources[] = $attribute->name;
        }

        return $sources;
    }

    protected function get_target(): string {
        /* @var Target $attribute */
        $attribute = $this->reflection->attributes[Target::class];

        return $attribute->name;
    }

    protected function get_target_type(): ?string {
        /* @var Target $attribute */
        $attribute = $this->reflection->attributes[Target::class];

        return $attribute->type_name;
    }

    protected function get_indexer(): Indexer {
        $new = "{$this->name}::new";
        return $new(['index' => $this]);
    }

    protected function get_properties(): array {
        $properties = [];

        foreach ($this->reflection->methods as $method) {
            if (!str_starts_with($method->name, 'index_')) {
                continue;
            }

            $name = substr($method->name, strlen('index_'));

            $parameters = [];

            foreach ($method->reflection->getParameters() as $parameter) {
                $parameters[] = $parameter->getName();
            }

            $properties[$name] = Property::new([
                'index' => $this,
                'name' => $name,
                'parameters' => $parameters,
            ]);
        }

        foreach ($properties as $property) {
            $property->after = array_filter($property->parameters,
                fn(string $name) => isset($properties[$name]));
        }

        return sort_by_dependency($properties, __("Properties"),
            fn($positions) =>
                fn(Property $a, Property $b) =>
                    $positions[$a->name] <=> $positions[$b->name]
        );
    }

    public function __wakeup(): void
    {
        foreach ($this->properties as $property) {
            $property->index = $this;
        }
    }
}