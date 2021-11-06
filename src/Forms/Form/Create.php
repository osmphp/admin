<?php

namespace Osm\Admin\Forms\Form;

use Osm\Admin\Forms\Form;
use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\Required;
use Osm\Admin\Forms\Routes;

/**
 * @property string $title
 * @property string $save_url
 */
#[Name('create')]
class Create extends Form
{
    public string $template = 'forms::form.create';

    protected function get_title(): string {
        throw new Required(__METHOD__);
    }

    protected function get_routes(): array {
        $data = [
            'data_class_name' => $this->data_class_name,
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