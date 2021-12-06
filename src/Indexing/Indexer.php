<?php

namespace Osm\Admin\Indexing;

use Osm\Admin\Base\Attributes\Markers\On;
use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\__;
use function Osm\sort_by_dependency;

/**
 * @property string $name #[Serialized]
 * @property Event[] $events #[Serialized]
 * @property Property[] $properties #[Serialized]
 * @property string[] $depends_on #[Serialized]
 *
 * @property Module $indexing
 */
class Indexer extends Object_
{
    public function index(Event $event = null): void {
        throw new NotImplemented($this);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_events(): array {
        global $osm_app; /* @var App $osm_app */

        $events = [];

        foreach ($this->__class->attributes as
            $attributeClassName => $attributes)
        {
            if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
                continue;
            }

            /* @var On $marker */
            if (!($marker = $class->attributes[On::class] ?? null)) {
                continue;
            }

            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            $new = "{$osm_app->classes[Event::class]
                ->getTypeClassName($marker->type ?? null)}::new";

            foreach ($attributes as $attribute) {
                $event = $new(array_merge(['indexer' => $this],
                    (array)$attribute));

                $event->id = $this->indexing->event_ids
                    ["{$this->name}|{$event->alias}"] ?? null;

                $events[$event->alias] = $event;
            }
        }

        return $events;
    }

    protected function get_properties(): array {
        $properties = [];

        foreach ($this->__class->methods as $method) {
            if (!str_starts_with($method->name, 'index_')) {
                continue;
            }

            $name = substr($method->name, strlen('index_'));

            $dependsOn = [];

            foreach ($method->reflection->getParameters() as $parameter) {
                $dependsOn[] = str_replace('__', '.',
                    $parameter->getName());
            }

            $properties[$name] = Property::new([
                'index' => $this,
                'name' => $name,
                'depends_on' => $dependsOn,
            ]);
        }

        foreach ($properties as $property) {
            $property->after = array_filter($property->depends_on,
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
            $property->indexer = $this;
        }

        foreach ($this->events as $event) {
            $event->indexer = $this;
        }
    }

    protected function get_depends_on(): array {
        $dependsOn = ['id' => true];

        foreach ($this->properties as $property) {
            foreach ($property->depends_on as $formula) {
                if (!isset($this->properties[$formula])) {
                    $dependsOn[$formula] = true;
                }
            }
        }

        return array_keys($dependsOn);
    }

    protected function get_indexing(): Module|BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[Module::class];
    }

    public function dirty(): bool {
        return (bool)$this->db->table('events')
            ->where('indexer', $this->name)
            ->where('dirty', true)
            ->value('dirty');
    }

    public function clearDirtyFlag(): void
    {
        $this->db->table('events')
            ->where('indexer', $this->name)
            ->update(['dirty' => false]);
    }
}