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
        return [
            $this->area_class_name => [
                "GET {$this->url}" => [ Routes\Admin\RenderCreateFormPage::class => [
                    'data_class_name' => $this->data_class_name,
                    'form_name' => $this->name,
                ]],
            ],
        ];
    }

    protected function get_save_url(): string {
        global $osm_app; /* @var App $osm_app */

        $url = mb_substr($this->url, 0, mb_strrpos($this->url, '/'));

        return "{$osm_app->area_url}{$url}/save";
    }
}