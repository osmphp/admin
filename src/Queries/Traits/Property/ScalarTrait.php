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

        $identifier->data_type = $this->data_type;
        $identifier->array = $this->array;
    }

    public function insert(array &$inserts, mixed $value)
        : void
    {
        /* @var Property|Property\Scalar|static $this */

        if ($this->explicit) {
            $inserts[$this->name] = $value;
            return;
        }

        if ($value === null) {
            return;
        }

        if (!isset($inserts['data'])) {
            $inserts['data'] = new \stdClass();
        }

        $inserts['data']->{$this->name} = $value;
    }

    public function update(array &$updates, mixed $value): void {
        /* @var Property|Property\Scalar|static $this */

        if ($this->explicit) {
            $updates[$this->name] = ["?", [$value]];
            return;
        }

        list($sql, $bindings) = $updates['data'] ?? ["`data`", []];

        $updates['data'] = $value === null
            ? ["JSON_REMOVE({$sql}, '$.\"{$this->name}\"')", $bindings]
            : ["JSON_SET({$sql}, '$.\"{$this->name}\"', ?)",
                array_merge($bindings, [$value])];
    }
}