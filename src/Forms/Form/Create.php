<?php

namespace Osm\Admin\Forms\Form;

use Osm\Admin\Forms\Form;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\Required;

/**
 * @property string $title
 */
#[Name('create')]
class Create extends Form
{
    protected function get_title(): string {
        throw new Required(__METHOD__);
    }
}