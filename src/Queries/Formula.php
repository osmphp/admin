<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Schema\Struct;
use Osm\Admin\Schema\Table;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Formula $parent
 * @property string $type #[Serialized]
 * @property string $formula #[Serialized]
 * @property int $pos #[Serialized]
 * @property int $length #[Serialized]
 *
 * Resolved properties:
 *
 * @property string $data_type #[Serialized]
 *
 * @uses Serialized
 */
class Formula extends Object_
{
    const SORT_EXPR = 'sort_expr';              // SortExpr
    const SELECT_EXPR = 'select_expr';          // SelectExpr
    const EXPR = 'ternary';                     // synonym for TERNARY
    const IDENTIFIER = 'identifier';            // Identifier
    const LOGICAL_OR = 'logical_or';            // Operator
    const LOGICAL_XOR = 'logical_xor';          // Operator
    const LOGICAL_AND = 'logical_and';          // Operator
    const LOGICAL_NOT = 'logical_not';          // Unary
    const IS_NULL = 'is_null';                  // Unary
    const IS_NOT_NULL = 'is_not_null';          // Unary
    const EQUAL = 'equal';                      // Operator
    const EQUAL_OR_GREATER = 'equal_or_greater';// Operator
    const GREATER = 'greater';                  // Operator
    const EQUAL_OR_LESS = 'equal_or_less';      // Operator
    const LESS = 'less';                        // Operator
    const NOT_EQUAL = 'not_equal';              // Operator
    const EQUAL_OR_NULL = 'equal_or_null';      // Operator
    const BIT_OR = 'bit_or';                    // Operator
    const NOT_IN = 'not_in';                    // In_
    const IN_ = 'in';                           // In_
    const NOT_BETWEEN = 'not_between';          // Between
    const BETWEEN = 'between';                  // Between
    const NOT_LIKE = 'not_like';                // Pattern
    const LIKE = 'like';                        // Pattern
    const NOT_REGEXP = 'not_regexp';            // Pattern
    const REGEXP = 'regexp';                    // Pattern
    const BIT_AND = 'bit_and';                  // Operator
    const BIT_SHIFT = 'bit_shift';              // Operator
    const ADD = 'add';                          // Operator
    const MULTIPLY = 'multiply';                // Operator
    const BIT_XOR = 'bit_xor';                  // Operator
    const POSITIVE = 'positive';                // Unary
    const NEGATIVE = 'negative';                // Unary
    const BIT_INVERT = 'bit_invert';            // Unary
    const PARAMETER = 'parameter';              // Parameter
    const CALL = 'call';                        // Call
    const LITERAL = 'literal';                  // Literal
    const COALESCE = 'coalesce';                // Operator
    const TERNARY = 'ternary';                  // Ternary
    const CAST = 'cast';                        // Cast

    // formula types only used internally inside parser
    const SIGNED_SIMPLE = self::POSITIVE;

    protected function get_type(): string {
        throw new Required(__METHOD__);
    }

    protected function get_formula(): string {
        throw new Required(__METHOD__);
    }

    protected function get_pos(): int {
        throw new Required(__METHOD__);
    }

    protected function get_length(): int {
        throw new Required(__METHOD__);
    }

    public function resolve(Table $table): void
    {
        throw new NotImplemented($this);
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        throw new NotImplemented($this);
    }
}