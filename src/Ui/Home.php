<?php

namespace Osm\Admin\Ui;

use Osm\Framework\Blade\View;
use function Osm\__;
use function Osm\view;

class Home extends View
{
    public $template = 'ui::home';

    protected function get_data(): array
    {
        return [
            'title' => __("Home"),
            'sidebar' => view(Sidebar::class),
        ];
    }
}