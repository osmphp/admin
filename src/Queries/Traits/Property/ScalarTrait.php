<?php

namespace Osm\Admin\Queries\Traits\Property;

use Osm\Admin\Queries\Exceptions\InvalidIdentifier;
use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Traits\PropertyTrait;
use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Property\Scalar;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;

#[UseIn(Scalar::class)]
trait ScalarTrait
{
    use PropertyTrait;

    public function resolve(Formula\Identifier $identifier): void
    {
        /* @var Property|Property\Scalar|static $this */

        if ($this->explicit) {
            $identifier->column = $this->name;
        }
        else {
            if (!$identifier->path) {
                $identifier->path = '$.';
            }

            if (!$identifier->column) {
                $identifier->column = 'data';
            }

            $identifier->path .= '"' . $this->name . '"';
        }

        $identifier->data_type = $this->type;
        $identifier->array = $this->array;
    }

    public function assign(array &$assignments, mixed $value): void {
        /* @var Property|Property\Scalar|static $this */

        if ($this->explicit) {
            $assignments[$this->name] = $value === null
                ? ["NULL", []]
                : ["?", $value];
            return;
        }

        $assignment = $assignments['data'] ?? ["`data`", []];
        list($sql, $bindings) = $assignment;
        $assignments['data'] = $value === null
            ? ["JSON_REMOVE({$sql}, '$.\"{$this->name}\"')", $bindings]
            : ["JSON_SET({$sql}, '$.\"{$this->name}\"', ?)",
                array_merge($bindings, [$value])];
    }
}