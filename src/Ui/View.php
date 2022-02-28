<?php

namespace Osm\Admin\Ui;

use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $name #[Serialized]
 * @property string $view_class_name
 *
 * @uses Serialized
 */
class View extends Object_
{
    protected function get_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_view_class_name(): string {
        throw new Required(__METHOD__);
    }

    public function view(array $data = []): View\View {
        $new = "{$this->view_class_name}::new";

        return $new(array_merge(['model' => $this], $data));
    }
}