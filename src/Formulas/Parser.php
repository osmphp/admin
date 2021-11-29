<?php

namespace Osm\Admin\Formulas;

use Osm\Admin\Base\Exceptions\SyntaxError;
use Osm\Admin\Base\Exceptions\UndefinedProperty;
use Osm\Admin\Schema\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use function Osm\__;

/**
 * @property Class_ $class Reset before each parse().
 * @property string $text Reset before each parse().
 */
class Parser extends Object_
{
    public const IDENTIFIER_PATTERN = '[_a-zA-Z][_a-zA-Z0-9]*';
    public const QUALIFIED_IDENTIFIER_PATTERN = '/^' .
        '(?<namespace>(?:' . self::IDENTIFIER_PATTERN . ')\.)*' .
        '(?<identifier>' . self::IDENTIFIER_PATTERN . '|\*)' .
        '$/u';
    public function parse(string $text, Class_ $class): Formula {
        $this->class = $class;
        $this->text = $text;

        // for now, the parser only recognizes qualified identifiers, that is,
        // `parent.title`. Later, it will be replaced with a recursive descent
        // parser, and it will support more sophisticated expressions, such as
        // arithmetic operations, comparison operations, function calls,
        // and more. We've already been there, see
        // https://github.com/osmphp/framework/blob/old_v4/src/Data/Formulas/Parser/Parser.php
        // for more details
        return $this->parseIdentifierBySplittingString();
    }

    protected function parseIdentifierBySplittingString(): Formula
    {
        $wildcard = false;
        $identifiers = explode('.', $this->text);

        if ($identifiers[count($identifiers) - 1] == '*') {
            $wildcard = true;
            array_pop($identifiers);
        }

        $class = $this->class;
        $accessors = [];
        foreach ($identifiers as $identifier) {
            if (!$class) {
                throw new SyntaxError(__("Can't use dot syntax after scalar property in ':formula' formula", [
                    'formula' => $this->text,
                ]));
            }

            if (!($property = $class->properties[$identifier] ?? null)) {
                throw new UndefinedProperty(__("':property' property, referenced in ':formula' formula, is not defined in ':class' class.", [
                    'property' => $identifier,
                    'formula' => $this->text,
                    'class' => $class->name,
                ]));
            }

            $accessors[] = $property;
            $class = $class->schema->classes[$property->reflection->type] ?? null;
        }

        $property = $wildcard ? null : array_pop($accessors);

        return Formula\Identifier::new([
            'text' => $this->text,
            'accessors' => $accessors,
            'property' => $property,
            'wildcard' => $wildcard,
        ]);
    }
}