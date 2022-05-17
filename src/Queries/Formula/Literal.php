<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Parser;
use Osm\Admin\Schema\Table;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property string $value #[Serialized]
 * @property int $token #[Serialized]
 *
 * @uses Serialized
 */
class Literal extends Formula
{
    public $type = self::LITERAL;

    protected function get_value(): string {
        throw new Required(__METHOD__);
    }

    protected function get_token(): int {
        throw new Required(__METHOD__);
    }

    public function resolve(Table $table): void
    {
        $this->data_type = $this->data_types[Parser::$literals[$this->token]];
        $this->array = false;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        $bindings[] = $this->value();

        return '?';
    }

    public function value(): mixed {
        return match($this->token) {
            Parser::STRING_ => Parser::unescapeString($this->value),
            Parser::INT_ => intval($this->value),
            Parser::FLOAT_ => floatval($this->value),
            Parser::HEXADECIMAL => hexdec(mb_substr($this->value, 2)),
            Parser::BINARY => bindec(mb_substr($this->value, 2)),
            Parser::TRUE_ =>true,
            Parser::FALSE_ => false,
            Parser::NULL_ => null,
            default =>
            throw new NotSupported(__(
                "Literal token type ':type' not supported",
                ['type' => Parser::getTokenTitle(Parser::STRING_)])),
        };
    }
}