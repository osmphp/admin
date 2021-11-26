<?php

namespace Osm\Admin\Queries\Expression;

use Osm\Admin\Queries\Expression;
use Osm\Admin\Queries\Select;
use Osm\Admin\Schema\Property;
use Osm\Core\Attributes\Type;

/**
 * @property Property[] $properties
 */
#[Type('wildcard')]
class Wildcard extends Expression
{
    public function select(): void
    {
        $class = empty($this->properties)
            ? $this->query->class
            : end($this->properties)->class;

        $textPrefix = empty($this->properties)
            ? ''
            : substr($this->text, 0, strlen($this->text) - 1);

        foreach ($class->properties as $property) {
            $text = "{$textPrefix}{$property->name}";
            $this->query->selects[$text] = Select::new([
                'query' => $this->query,
                'expression' => Identifier::new([
                    'query' => $this->query,
                    'text' => $text,
                    'properties' => array_merge($this->properties,
                        [$property->name => $property]),
                ]),
            ]);
        }
    }
}