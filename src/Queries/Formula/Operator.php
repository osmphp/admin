<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property Formula[] $operands #[Serialized]
 * @property int[] $operators #[Serialized]
 *
 * @uses Serialized
 */
class Operator extends Formula
{
    protected function get_operands(): array {
        throw new Required(__METHOD__);
    }

    protected function get_operators(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void
    {
        foreach ($this->operands as $operand) {
            $operand->parent = $this;
        }
    }

    public function resolve(Table $table): void
    {
        foreach ($this->operands as $operand) {
            $operand->resolve($table);
        }

        switch ($this->type) {
            case Formula::LOGICAL_OR:
            case Formula::LOGICAL_XOR:
            case Formula::LOGICAL_AND:
                $this->castOperandsTo('bool');
                $this->data_type = $this->data_types['bool'];
                $this->array = false;
                break;

            case Formula::EQUAL:
            case Formula::EQUAL_OR_GREATER:
            case Formula::GREATER:
            case Formula::EQUAL_OR_LESS:
            case Formula::LESS:
            case Formula::NOT_EQUAL:
            case Formula::EQUAL_OR_NULL:
                $this->data_type = $this->data_types['bool'];
                $this->array = false;
                break;

            case Formula::BIT_OR:
            case Formula::BIT_AND:
            case Formula::BIT_SHIFT:
            case Formula::ADD:
            case Formula::MULTIPLY:
            case Formula::BIT_XOR:
            case Formula::COALESCE:
                $this->data_type = $this->data_types[$this->castOperandsToFirstNonNull()];
                $this->array = false;
                break;
            default:
                throw new NotSupported(__(
                    "':type' operator is not supported",
                    ['type' => $this->type]));
        }
    }

    protected function castOperandsTo(string $dataType): void {
        foreach ($this->operands as &$operand) {
            $operand = $operand->castTo($dataType);
        }
    }

    protected function castOperandsToFirstNonNull(): string {
        $dataType = 'mixed';
        foreach ($this->operands as $operand) {
            if ($operand->data_type->type !== 'mixed') {
                $dataType = $operand->data_type->type;
                break;
            }
        }

        $this->castOperandsTo($dataType);

        return $dataType;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        $sql = '';

        switch ($this->type) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case Formula::ADD:
                if ($this->data_type->type == 'string') {
                    foreach ($this->operands as $operand) {
                        if ($operand instanceof Formula\Literal && $operand->value == '') {
                            continue;
                        }

                        if ($sql) {
                            $sql .= ", ";
                        }

                        $sql .= $operand->toSql($bindings, $from, $join);
                    }

                    return $sql ? "CONCAT($sql)" : "''";
                }
            case Formula::LOGICAL_OR:
            case Formula::LOGICAL_AND:
            case Formula::EQUAL:
            case Formula::EQUAL_OR_GREATER:
            case Formula::GREATER:
            case Formula::EQUAL_OR_LESS:
            case Formula::LESS:
            case Formula::NOT_EQUAL:
                $sql .= "({$this->operands[0]->toSql(
                    $bindings, $from, $join)})";

                foreach ($this->operators as $i => $operator) {
                    $sql .= Query::$operators[$operator];
                    $sql .= "({$this->operands[$i + 1]->toSql(
                        $bindings, $from, $join)})";
                }
                return $sql;

            case Formula::COALESCE:
                foreach ($this->operands as $operand) {
                    if ($sql) {
                        $sql .= ", ";
                    }

                    $sql .= $operand->toSql($bindings, $from, $join);
                }

                return "COALESCE($sql)";

            case Formula::EQUAL_OR_NULL:
                $first = $this->operands[0];
                $second = $this->operands[1];
                return !($second instanceof Formula\Parameter) || $second->parameter !== null
                    ? "{$first->toSql($bindings, $from, $join)} = " .
                        "{$second->toSql($bindings, $from, $join)} OR " .
                        "{$first->toSql($bindings, $from, $join)} IS NULL"
                    : "{$first->toSql($bindings, $from, $join)} IS NULL";

            default:
                throw new NotSupported(__(
                    "':type' operator is not supported",
                    ['type' => $this->type]));
       }
    }
}