<?php

namespace Osm\Admin\Schema\Traits;

use Osm\Admin\Schema\Attribute;
use Osm\Core\Class_;
use Osm\Core\Property;

/**
 * @property Class_|Property $reflection
 */
trait AttributeParser
{
    public function parseAttributes(): void
    {
        foreach ($this->parseAttributeData($this->reflection) as $key => $value)
        {
            $this->$key = $value;
        }
    }

    protected function parseAttributeData(Class_|Property $reflection)
        : \stdClass
    {
        $data = new \stdClass();

        foreach ($reflection->attributes as $attributes) {
            if (!is_array($attributes)) {
                $attributes = [$attributes];
            }

            foreach ($attributes as $attribute) {
                if ($attribute instanceof Attribute) {
                    $attribute->parse($data);
                }
            }
        }

        return $data;
    }
}