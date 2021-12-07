<?php

namespace Osm\Admin\Forms\Form;

use Osm\Admin\Forms\Form;
use Osm\Core\Attributes\Type;
use Osm\Core\Exceptions\Required;
use Osm\Admin\Forms\Routes;

/**
 * @property string $title
 * @property string $save_url
 */
#[Type('create')]
class Create extends Form
{
    public string $template = 'forms::form.create';

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_routes(): array {
        $data = [
            'class_name' => $this->class->name,
            'form_name' => $this->name,
        ];

        return [
            $this->area_class_name => [
                "GET {$this->url}" => [ Routes\Admin\CreatePage::class => $data],
                "POST {$this->save_url}" => [ Routes\Admin\Save::class => $data],
            ],
        ];
    }

    protected function get_save_url(): string {
        return mb_substr($this->url, 0, mb_strrpos($this->url, '/')) . '/save';
    }
}