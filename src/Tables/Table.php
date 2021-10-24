<?php

namespace Osm\Data\Tables;

use Osm\Core\Exceptions\AttributeRequired;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Tables\Attributes;

/**
 * @property string $record_class_name #[Serialized]
 */
class Table extends Object_
{
    protected function get_record_class_name(): string {
        /* @var Attributes\Record $attribute */
        if (!($attribute = $this->__class->attributes[Attributes\Record::class]
            ?? null))
        {
            throw new AttributeRequired(Attributes\Record::class,
                $this->__class->name);
        }

        return $attribute->class_name;
    }
}