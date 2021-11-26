<?php

namespace Osm\Admin\Queries\Filter;

use Osm\Admin\Queries\Filter;

class Logical extends Filter
{
    /**
     * @var Filter[]
     */
    public array $operands = [];

    public function equals(string $expression, mixed $value): static {
        $this->operands[] = Filter\Property\Equals::new([
            'query' => $this->query,
            'parent' => $this,
            'expression' => $this->query->parseExpression($expression),
            'value' => $value,
        ]);

        return $this;
    }

    public function and(callable $callback): static {
        $this->operands[] = $filter = Filter\Logical\And_::new([
            'query' => $this->query,
            'parent' => $this,
        ]);

        $callback($filter);

        return $this;
    }
}