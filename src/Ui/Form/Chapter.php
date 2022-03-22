<?php

namespace Osm\Admin\Ui\Form;

use Osm\Admin\Ui\Form;
use Osm\Admin\Ui\Query;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Framework\Blade\View;
use Osm\Core\Attributes\Serialized;

/**
 * @property Form $form
 * @property string $name #[Serialized]
 * @property array $layout #[Serialized]
 * @property Section[] $sections #[Serialized]
 *
 * @uses Serialized
 */
class Chapter extends View
{
    public string $template = 'ui::form.chapter';

    public function prepare(Query $query): void
    {
        foreach ($this->sections as $section) {
            $section->prepare($query);
        }
    }

    public function merge(\stdClass $merged): void
    {
        foreach ($this->sections as $section) {
            $section->merge($merged);
        }
    }

    protected function get_form(): Form {
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
            'chapter' => $this,
        ];
    }

    protected function get_sections(): array {
        $sections = [];

        foreach ($this->layout as $name => $data) {
            $data['name'] = $name;
            $data['chapter'] = $this;
            $sections[$name] = Section::new($data);
        }

        return $sections;
    }

    public function __wakeup(): void
    {
        foreach ($this->sections as $section) {
            $section->chapter = $this;
        }
    }
}