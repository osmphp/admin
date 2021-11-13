<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Base\Attributes\Markers\Form\Chapter as ChapterMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Section as SectionMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Group as GroupMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Field as FieldMarker;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;

/**
 * @property Class_ $class
 * @property string $area_class_name #[Serialized]
 * @property ?string $name #[Serialized]
 * @property string $url #[Serialized]
 * @property Chapter[] $chapters #[Serialized]
 * @property Section[] $sections #[Serialized]
 * @property Group[] $groups #[Serialized]
 * @property Field[] $fields #[Serialized]
 * @property array $routes #[Serialized]
 * @property string $template #[Serialized]
 */
class Form extends Object_
{
    use SubTypes;

    protected function get_class(): Class_ {
        throw new Required(__METHOD__);
    }

    protected function get_area_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_url(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        return "{$this->area_class_name}:{$this->url}";
    }

    protected function get_chapters(): array {
        $chapters = $this->fromClassAttributes(
            ChapterMarker::class, Chapter::class);

        if (!isset($chapters['implicit'])) {
            $chapters['implicit'] = Chapter\Standard::new([
                'form' => $this,
                'sort_order' => 0,
                'name' => 'implicit',
                'title' => null,
            ]);
        }

        uasort($chapters, fn(Chapter $a, Chapter $b) =>
            $a->sort_order <=> $b->sort_order);

        return $chapters;
    }

    protected function get_sections(): array {
        $sections = $this->fromClassAttributes(
            SectionMarker::class, Section::class);

        if (!isset($sections['implicit'])) {
            $sections['implicit'] = Section\Standard::new([
                'form' => $this,
                'sort_order' => 0,
                'name' => 'implicit',
                'chapter_name' => 'implicit',
                'title' => 'General',
            ]);
        }

        uasort($sections, fn(Group $a, Group $b) =>
            $a->sort_order <=> $b->sort_order);

        return $sections;
    }

    protected function get_groups(): array {
        $groups = $this->fromClassAttributes(
            GroupMarker::class, Group::class);

        if (!isset($groups['implicit'])) {
            $groups['implicit'] = Group\Standard::new([
                'form' => $this,
                'sort_order' => 0,
                'name' => 'implicit',
                'section_name' => 'implicit',
                'title' => null,
            ]);
        }

        uasort($groups, fn(Group $a, Group $b) =>
            $a->sort_order <=> $b->sort_order);

        return $groups;
    }

    protected function get_fields(): array {
        $fields = $this->fromFieldAttributes(
            FieldMarker::class, Field::class);

        uasort($fields, fn(Field $a, Field $b) =>
            $a->sort_order <=> $b->sort_order);

        return $fields;
    }

    protected function createParts(string $markerClassName,
        string $partClassName, string $attributeClassName,
        array $attributes): array
    {
        global $osm_app; /* @var App $osm_app */

        if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
            return [];
        }

        if (!($marker = $class->attributes[$markerClassName] ?? null)) {
            return [];
        }

        $partClassNames = $osm_app->descendants
            ->byName($partClassName);
        $new = "{$partClassNames[$marker->type]}::new";

        $parts = [];

        foreach ($attributes as $attribute) {
            $part = $new(array_merge(['form' => $this], (array)$attribute));
            $parts[$part->name] = $part;
        }

        return $parts;
    }

    protected function fromClassAttributes(string $markerClassName,
        string $partClassName): array
    {
        $parts = [];

        foreach ($this->class->reflection->attributes as
                 $className => $attributes)
        {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            $parts = array_merge($parts, $this->createParts($markerClassName,
                $partClassName, $className, $attributes));
        }

        return $parts;
    }

    protected function fromFieldAttributes(string $markerClassName,
        string $partClassName): array
    {
        global $osm_app; /* @var App $osm_app */

        $parts = [];

        foreach ($this->class->properties as $property) {
            foreach ($property->reflection->attributes as
                     $attributeClassName => $attribute)
            {
                if (!($class = $osm_app->classes[$attributeClassName] ?? null)) {
                    continue;
                }

                if (!($marker = $class->attributes[$markerClassName] ?? null)) {
                    continue;
                }

                $partClassNames = $osm_app->descendants->byName($partClassName);
                $new = "{$partClassNames[$marker->type]}::new";

                $part = $new(array_merge([
                    'form' => $this,
                    'name' => $property->name,
                ], (array)$attribute));
                $parts[$part->name] = $part;
            }
        }

        return $parts;
    }

    protected function get_routes(): array {
        return [];
    }

    protected function get_template(): string {
        throw new Required(__METHOD__);
    }
}