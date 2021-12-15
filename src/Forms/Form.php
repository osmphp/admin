<?php

namespace Osm\Admin\Forms;

use Osm\Admin\Base\Attributes\Markers\Form\Chapter as ChapterMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Section as SectionMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Group as GroupMarker;
use Osm\Admin\Base\Attributes\Markers\Form\Field as FieldMarker;
use Osm\Admin\Interfaces\Interface_;
use Osm\Admin\Schema\Class_;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;
use function Osm\__;

/**
 * @property Interface_ $interface
 * @property Class_ $class
 * @property Chapter[] $chapters #[Serialized]
 */
class Form extends Object_
{
    public string $template = 'forms::form';

    public const EDIT_MODE = 'edit';
    public const CREATE_MODE = 'create';
    public const MASS_EDIT_MODE = 'mass_edit';
    public const VIEW_MODE = 'view';

    use SubTypes;

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

    public function options(): array {
        return [
            's_saving_new_object' => __($this->interface->s_saving_new_object),
            's_new_object_saved' => __($this->interface->s_new_object_saved),
        ];
    }
}