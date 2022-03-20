<?php

namespace Osm\Admin\Ui\Form;

use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Core\Attributes\Serialized;

/**
 * @property Chapter $chapter
 * @property Form $form
 * @property string $name #[Serialized]
 * @property array $layout #[Serialized]
 * @property Fieldset[] $fieldsets #[Serialized]
 *
 * @uses Serialized
 */
class Section extends View
{
    public string $template = 'ui::form.section';

    protected function get_chapter(): Chapter {
        throw new Required(__METHOD__);
    }

    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_layout(): array {
        throw new Required(__METHOD__);
    }

    protected function get_data(): array {
        return [
            'section' => $this,
        ];
    }

    protected function get_fieldsets(): array {
        $fieldsets = [];

        foreach ($this->layout as $name => $data) {
            $data['name'] = $name;
            $data['section'] = $this;
            $fieldsets[$name] = Fieldset::new($data);
        }

        return $fieldsets;
    }

    public function __wakeup(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            $fieldset->section = $this;
        }
    }

    protected function get_form(): Form {
        return $this->chapter->form;
    }

    public function prepare(Query $query): void
    {
        foreach ($this->fieldsets as $fieldset) {
            $fieldset->prepare($query);
        }
    }
}