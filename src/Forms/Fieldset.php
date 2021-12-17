<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Base\Attributes\Markers\FormField as FieldMarker;
use Osm\Core\App;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Section $section
 * @property int $sort_order #[Serialized]
 * @property string $name #[Serialized]
 * @property ?string $title #[Serialized]
 * @property Field[] $fields #[Serialized]
 */
class Fieldset extends Object_
{
    public string $template = 'forms::fieldset';

    protected function get_section(): Section {
        throw new Required(__METHOD__);
    }

    protected function get_sort_order(): string {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_fields(): array {
        global $osm_app; /* @var App $osm_app */

        $fields = [];

        $section = $this->section;
        $chapter = $section->chapter;
        $properties = $chapter->form->interface->class->properties;

        foreach ($properties as $property) {
            foreach ($property->reflection->attributes as
                     $attributeClassName => $attribute)
            {
                if (!($attributeClass = $osm_app->classes[$attributeClassName]
                    ?? null))
                {
                    continue;
                }

                /* @var FieldMarker $marker */
                if (!($marker = $attributeClass->attributes[FieldMarker::class]
                    ?? null))
                {
                    continue;
                }

                $data = (array)$attribute;
                $in = $data['in'] ?? '//';
                unset($data['in']);
                if ($in != "{$chapter->name}/{$section->name}/{$this->name}") {
                    continue;
                }

                $new = $osm_app->classes[Field::class]
                        ->getTypeClassName($marker->type) . "::new";

                $fields[$property->name] = $new(array_merge([
                    'fieldset' => $this,
                    'name' => $property->name,
                ], $data));

                break;
            }
        }

        return $fields;
    }

    public function __wakeup(): void
    {
        foreach ($this->fields as $field) {
            $field->fieldset = $this;
        }
    }
}