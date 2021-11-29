<?php

namespace Osm\Admin\Queries\Traits;

use Osm\Admin\Queries\Query;
use Osm\Admin\Formulas\Formula;
use Osm\Core\Attributes\UseIn;
use function Osm\formula;

#[UseIn(Formula\Operator::class)]
trait Where
{
    public function equals(string $formula, mixed $value): static {
        $property = $this instanceof Query ? 'filters': 'operands';
        $this->{$property}[] = $parent = Formula\Operator\Equals::new([
            'operands' => [
                $expr = formula($formula, $this->class),
                $literal = Formula\Literal::new([
                    'value' => $value,
                ])
            ],
        ]);
        $expr->parent = $parent;
        $literal->parent = $parent;

        return $this;
    }

    public function and(callable $callback): static {
        $property = $this instanceof Query ? 'filters': 'operands';
        $this->{$property}[] = $parent = Formula\Operator\And_::new([
            'operands' => [],
        ]);

        $callback($parent);

        foreach ($parent->operands as $operand) {
            $operand->parent = $parent;
        }

        return $this;
    }
}