<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property Interface_ $interface
 * @property Class_ $class
 * @property Chapter[] $chapters #[Serialized]
 * @property array $http_query
 * @property string $mode
 */
class Form extends Object_
{
    public string $template = 'forms::form';

    protected function get_interface(): Interface_ {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        return $this->interface->class;
    }

    /**
     * @return Chapter[]
     */
    protected function load(): array {
        return [
            '' => Chapter::new([
                'sort_order' => 0,
                'sections' => [
                    '' => Section::new([
                        'sort_order' => 0,
                        'title' => 'General',
                        'fieldsets' => [
                            '' => Fieldset::new([
                                'sort_order' => 0,
                            ]),
                        ],
                    ])
                ],
            ])
        ];
    }

    protected function get_chapters(): array {
        $chapters = $this->load();

        foreach ($chapters as $chapterName => $chapter) {
            $chapter->form = $this;
            $chapter->name = $chapterName;

            foreach ($chapter->sections as $sectionName => $section) {
                $section->chapter = $chapter;
                $section->name = $sectionName;

                foreach ($section->fieldsets as $fieldsetName => $fieldset) {
                    $fieldset->section = $section;
                    $fieldset->name = $fieldsetName;
                }
            }
        }

        return $chapters;
    }

    public function __wakeup(): void
    {
        foreach ($this->chapters as $chapter) {
            $chapter->form = $this;
        }
    }

    /**
     * @return \Generator|Field[]
     */
    public function fields(): \Generator|array {
        foreach ($this->chapters as $chapter) {
            foreach ($chapter->sections as $section) {
                foreach ($section->fieldsets as $fieldset) {
                    foreach ($fieldset->fields as $field) {
                        yield $field;
                    }
                }
            }
        }
    }
}