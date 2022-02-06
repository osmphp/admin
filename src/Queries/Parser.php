<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Queries\Exceptions\InvalidParameters;
use Osm\Admin\Queries\Exceptions\SyntaxError;
use Osm\Admin\Queries\Exceptions\UnexpectedCharacter;
use Osm\Admin\Queries\Exceptions\UnexpectedEOF;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use function Osm\__;

/**
 * @property string $text
 * @property array $parameters
 */
class Parser extends Object_
{
    // major token classes
    const IDENTIFIER = 101;
    const STRING_ = 102;
    const HEXADECIMAL = 103;
    const BINARY = 104;
    const INT_ = 105;
    const FLOAT_ = 106;
    const EOF = 107;

    // special characters
    const EQ = 201;
    const GT = 202;
    const GT_EQ = 203;
    const DOUBLE_GT = 204;
    const LT = 205;
    const LT_EQ = 206;
    const LT_GT = 207;
    const DOUBLE_LT = 208;
    const NOT_EQ = 209;
    const OPEN_PAR = 210;
    const CLOSE_PAR = 211;
    const COMMA = 212;
    const PIPE = 213;
    const AMPERSAND = 214;
    const PLUS = 215;
    const MINUS = 216;
    const ASTERISK = 217;
    const SLASH = 218;
    const PERCENT = 219;
    const HAT = 220;
    const TILDE = 221;
    const COLON = 222;
    const DOT = 223;
    const QUESTION = 224;
    const DOUBLE_QUESTION = 225;
    const QUESTION_EQ = 226;

    // reserved keywords
    const AS_ = 301;
    const OR_ = 302;
    const XOR_ = 303;
    const AND_ = 304;
    const NOT = 305;
    const IS_ = 306;
    const NULL_ = 307;
    const IN_ = 308;
    const BETWEEN = 309;
    const LIKE = 310;
    const REGEXP = 311;
    const DIV = 312;
    const MOD = 313;
    const TRUE_ = 314;
    const FALSE_ = 315;
    const ASC = 316;
    const DESC = 317;

    const STATE_INITIAL = 0;
    const STATE_GT = 1;
    const STATE_LT = 2;
    const STATE_EXCLAMATION = 3;
    const STATE_QUESTION = 4;
    const STATE_IDENTIFIER = 5;
    const STATE_NUMERIC = 6;
    const STATE_HEXADECIMAL = 7;
    const STATE_BINARY = 8;
    const STATE_SINGLE_QUOTED_STRING = 9;
    const STATE_ESCAPE_SINGLE_QUOTED_STRING = 10;

    public static array $reserved_keywords = [
        'as' => self::AS_,
        'or' => self::OR_,
        'xor' => self::XOR_,
        'and' => self::AND_,
        'not' => self::NOT,
        'is' => self::IS_,
        'null' => self::NULL_,
        'in' => self::IN_,
        'between' => self::BETWEEN,
        'like' => self::LIKE,
        'regexp' => self::REGEXP,
        'div' => self::DIV,
        'mod' => self::MOD,
        'true' => self::TRUE_,
        'false' => self::FALSE_,
        'asc' => self::ASC,
        'desc' => self::DESC,
    ];

    public static array $operators = [
        Formula::LOGICAL_OR => [
            'tokens' => [self::OR_ => true],
            'operand' => Formula::LOGICAL_XOR,
        ],
        Formula::LOGICAL_XOR => [
            'tokens' => [self::XOR_ => true],
            'operand' => Formula::LOGICAL_AND,
        ],
        Formula::LOGICAL_AND => [
            'tokens' => [self::AND_ => true],
            'operand' => Formula::LOGICAL_NOT,
        ],
        Formula::COALESCE => [
            'tokens' => [self::DOUBLE_QUESTION => true],
            'operand' => Formula::BIT_OR,
        ],
        Formula::BIT_OR => [
            'tokens' => [self::PIPE => true],
            'operand' => Formula::BIT_AND,
        ],
        Formula::BIT_AND => [
            'tokens' => [self::AMPERSAND => true],
            'operand' => Formula::BIT_SHIFT,
        ],
        Formula::BIT_SHIFT => [
            'tokens' => [self::DOUBLE_LT => true, self::DOUBLE_GT => true],
            'operand' => Formula::ADD,
        ],
        Formula::ADD => [
            'tokens' => [self::PLUS => true, self::MINUS => true],
            'operand' => Formula::MULTIPLY,
        ],
        Formula::MULTIPLY => [
            'tokens' => [self::ASTERISK => true, self::SLASH => true,
                self::DIV => true, self::MOD => true, self::PERCENT => true],
            'operand' => Formula::BIT_XOR,
        ],
        Formula::BIT_XOR => [
            'tokens' => [self::HAT => true],
            'operand' => Formula::SIGNED_SIMPLE,
        ],
    ];

    public static array $comparison_operators = [
        self::EQ => Formula::EQUAL,
        self::GT_EQ => Formula::EQUAL_OR_GREATER,
        self::GT => Formula::GREATER,
        self::LT_EQ => Formula::EQUAL_OR_LESS,
        self::LT => Formula::LESS,
        self::LT_GT => Formula::NOT_EQUAL,
        self::NOT_EQ => Formula::NOT_EQUAL,
        self::QUESTION_EQ => Formula::EQUAL_OR_NULL,
    ];

    public static array $signs = [
        self::PLUS => Formula::POSITIVE,
        self::MINUS => Formula::NEGATIVE,
        self::TILDE => Formula::BIT_INVERT,
    ];
    
    protected static string $identifier_starting_char =
        '_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected static string $identifier_char =
        '_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    protected static string $numeric_starting_char = '0123456789';
    protected static string $numeric_and_dot_char = '0123456789.';
    protected static string $numeric_char = '0123456789';
    protected static string $hexadecimal_char = 'abcdefABCDEF0123456789';
    protected static string $binary_char = '01';
    protected static string $escape_char = '0\'"bnrtZ\\%_';

    protected array $characters;
    protected int $length;
    protected int $pos = 0;
    protected int $previous_pos;
    protected int $token_pos;
    protected int $token_type;
    protected string $token_text;
    protected int $parameter_index = 0;
    protected int $parameter_count;

    protected function get_text(): string {
        throw new Required(__METHOD__);
    }

    protected function get_parameters(): array {
        throw new Required(__METHOD__);
    }

    #region Tokens
    public static function getTokenTitle(int $type): string {
        return match ($type) {
            static::IDENTIFIER => __("identifier"),
            static::STRING_ => __("string"),
            static::HEXADECIMAL => __("hexadecimal"),
            static::BINARY => __("binary"),
            static::INT_ => __("int"),
            static::FLOAT_ => __("float"),
            static::EOF => __("end of formula"),
            static::EQ => "'='",
            static::GT => "'>'",
            static::GT_EQ => "'>='",
            static::DOUBLE_GT => "'>>'",
            static::LT => "'<'",
            static::LT_EQ => "'<='",
            static::LT_GT => "'<>'",
            static::DOUBLE_LT => "'<<'",
            static::NOT_EQ => "'!='",
            static::OPEN_PAR => "'('",
            static::CLOSE_PAR => "')'",
            static::COMMA => "','",
            static::PIPE => "'|'",
            static::AMPERSAND => "'&'",
            static::PLUS => "'+'",
            static::MINUS => "'-'",
            static::ASTERISK => "'*'",
            static::SLASH => "'/'",
            static::PERCENT => "'%'",
            static::HAT => "'^'",
            static::TILDE => "'~'",
            static::COLON => "':'",
            static::DOT => "'.'",
            static::QUESTION => "'?'",
            static::DOUBLE_QUESTION => "'??'",
            static::AS_ => "'AS'",
            static::OR_ => "'OR'",
            static::XOR_ => "'XOR'",
            static::AND_ => "'AND'",
            static::NOT => "'NOT'",
            static::IS_ => "'IS'",
            static::NULL_ => "'NULL'",
            static::IN_ => "'IN'",
            static::BETWEEN => "'BETWEEN'",
            static::LIKE => "'LIKE'",
            static::REGEXP => "'REGEXP'",
            static::DIV => "'DIV'",
            static::MOD => "'MOD'",
            static::TRUE_ => "'TRUE'",
            static::FALSE_ => "'FALSE'",
            static::ASC => "'ASC'",
            static::DESC => "'DESC'",
            default => throw new NotImplemented(),
        };
    }

    public static function getTokenText(int $type): string {
        return match ($type) {
            static::EQ => "=",
            static::GT => ">",
            static::GT_EQ => ">=",
            static::DOUBLE_GT => ">>",
            static::LT => "<",
            static::LT_EQ => "<=",
            static::LT_GT => "<>",
            static::DOUBLE_LT => "<<",
            static::NOT_EQ => "!=",
            static::OPEN_PAR => "(",
            static::CLOSE_PAR => ")",
            static::COMMA => ",",
            static::PIPE => "|",
            static::AMPERSAND => "&",
            static::PLUS => "+",
            static::MINUS => "-",
            static::ASTERISK => "*",
            static::SLASH => "/",
            static::PERCENT => "%",
            static::HAT => "^",
            static::TILDE => "~",
            static::COLON => ":",
            static::DOT => ".",
            static::QUESTION => "?",
            static::DOUBLE_QUESTION => "??",
            static::AS_ => "AS",
            static::OR_ => "OR",
            static::XOR_ => "XOR",
            static::AND_ => "AND",
            static::NOT => "NOT",
            static::IS_ => "IS",
            static::NULL_ => "NULL",
            static::IN_ => "IN",
            static::BETWEEN => "BETWEEN",
            static::LIKE => "LIKE",
            static::REGEXP => "REGEXP",
            static::DIV => "DIV",
            static::MOD => "MOD",
            static::TRUE_ => "TRUE",
            static::FALSE_ => "FALSE",
            static::ASC => "ASC",
            static::DESC => "DESC",
            default => throw new NotImplemented(),
        };
    }

    public static function unescapeString(string $value): string {
        static $escapeChars;

        if (!$escapeChars) {
            $escapeChars = [
                '0' => "\0",
                '\'' => "'",
                'b' => chr(8),
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                'Z' => chr(26),
                '\\' => "\\",
                '%' => "\%",
                '_' => "\_",
            ];
        }

        $result = '';
        $inEscape = false;
        $length = mb_strlen($value);
        for ($i = 1; $i < $length - 1; $i++) {
            $ch = mb_substr($value, $i, 1);
            if ($inEscape) {
                if (isset($escapeChars[$ch])) {
                    $result .= $escapeChars[$ch];
                }
                else {
                    $result .= $ch;
                }
                $inEscape = false;
            }
            else {
                if ($ch == '\\') {
                    $inEscape = true;
                }
                else {
                    $result .= $ch;
                }
            }
        }

        return $result;
    }
    #endregion

    #region Scanner
    protected function scan(): void
    {
        $state = static::STATE_INITIAL;
        $pos = $this->previous_pos = $this->token_pos = $this->pos;
        $type = null;

        $hasDot = false;

        while ($this->pos < $this->length) {
            $ch = $this->characters[$this->pos++];

            switch ($state) {
                case static::STATE_INITIAL:
                    switch ($ch) {
                        case " ":
                        case "\t":
                        case "\n":
                        case "\r":
                        case "\0":
                        case "\x0B":
                            $pos++;
                            $this->token_pos++;
                            break;
                        case '=': $type = static::EQ; break;
                        case '>': $state = static::STATE_GT; break;
                        case '<': $state = static::STATE_LT; break;
                        case '!': $state = static::STATE_EXCLAMATION; break;
                        case '(': $type = static::OPEN_PAR; break;
                        case ')': $type = static::CLOSE_PAR; break;
                        case ',': $type = static::COMMA; break;
                        case '|': $type = static::PIPE; break;
                        case '&': $type = static::AMPERSAND; break;
                        case '+': $type = static::PLUS; break;
                        case '-': $type = static::MINUS; break;
                        case '*': $type = static::ASTERISK; break;
                        case '/': $type = static::SLASH; break;
                        case '%': $type = static::PERCENT; break;
                        case '^': $type = static::HAT; break;
                        case '~': $type = static::TILDE; break;
                        case ':': $type = static::COLON; break;
                        case '.': $type = static::DOT; break;
                        case '?': $state = static::STATE_QUESTION; break;
                        case "'": $state = static::STATE_SINGLE_QUOTED_STRING; break;
                        default:
                            if (mb_strpos(static::$identifier_starting_char, $ch) !== false) {
                                $state = static::STATE_IDENTIFIER;
                            }
                            elseif (mb_strpos(static::$numeric_char, $ch) !== false) {
                                $state = static::STATE_NUMERIC;
                            }
                            else {
                                throw $this->unexpectedCharacter();
                            }
                            break;
                    }
                    break;
                case static::STATE_IDENTIFIER:
                    if (mb_strpos(static::$identifier_char, $ch) === false) {
                        $type = static::IDENTIFIER;
                        $this->pos--;
                    }
                    break;
                case static::STATE_NUMERIC:
                    if ($ch == '.') {
                        if ($hasDot) {
                            throw $this->unexpectedCharacter();
                        }
                        else {
                            $hasDot = true;
                        }
                    }
                    elseif (mb_strpos(static::$numeric_char, $ch) === false) {
                        if ($this->pos - $pos == 2 && $this->characters[$pos] == '0' &&
                            ($ch == 'x' || $ch == 'b'))
                        {
                            $state = $ch == 'x' ? static::STATE_HEXADECIMAL : static::STATE_BINARY;
                        }
                        else {
                            $type = $hasDot ? static::FLOAT_ : static::INT_;
                            $this->pos--;
                        }
                    }
                    break;
                case static::STATE_HEXADECIMAL:
                    if (mb_strpos(static::$hexadecimal_char, $ch) === false) {
                        if ($this->pos - $pos <= 3) {
                            throw $this->unexpectedCharacter();
                        }
                        else {
                            $type = static::HEXADECIMAL;
                            $this->pos--;
                        }
                    }
                    break;
                case static::STATE_BINARY:
                    if (mb_strpos(static::$binary_char, $ch) === false) {
                        if ($this->pos - $pos <= 3) {
                            throw $this->unexpectedCharacter();
                        }
                        else {
                            $type = static::BINARY;
                            $this->pos--;
                        }
                    }
                    break;
                case static::STATE_SINGLE_QUOTED_STRING:
                    switch ($ch) {
                        case "'": $type = static::STRING_; break;
                        case '\\': $state = static::STATE_ESCAPE_SINGLE_QUOTED_STRING; break;
                    }
                    break;
                case static::STATE_ESCAPE_SINGLE_QUOTED_STRING:
                    // any character is acceptable after backslash
                    $state = static::STATE_SINGLE_QUOTED_STRING;
                    break;
                case static::STATE_GT:
                    switch ($ch) {
                        case '=': $type = static::GT_EQ; break;
                        case '>': $type = static::DOUBLE_GT; break;
                        default: $type = static::GT; $this->pos--; break;
                    }
                    break;
                case static::STATE_LT:
                    switch ($ch) {
                        case '=': $type = static::LT_EQ; break;
                        case '<': $type = static::DOUBLE_LT; break;
                        case '>': $type = static::LT_GT; break;
                        default: $type = static::LT; $this->pos--; break;
                    }
                    break;
                case static::STATE_EXCLAMATION:
                    $type = match ($ch) {
                        '=' => static::NOT_EQ,
                        default => throw $this->unexpectedCharacter(),
                    };
                    break;
                case static::STATE_QUESTION:
                    switch ($ch) {
                        case '?': $type = static::DOUBLE_QUESTION; break;
                        case '=': $type = static::QUESTION_EQ; break;
                        default: $type = static::QUESTION; $this->pos--; break;
                    }
                    break;
            }

            if ($type) {
                break;
            }
        }

        if ($type) {
            $this->token_type = $type;
        }
        else {
            switch ($state) {
                case static::STATE_INITIAL: $this->token_type = static::EOF; break;
                case static::STATE_IDENTIFIER: $this->token_type = static::IDENTIFIER; break;
                case static::STATE_GT: $this->token_type = static::GT; break;
                case static::STATE_LT: $this->token_type = static::LT; break;
                case static::STATE_QUESTION: $this->token_type = static::QUESTION; break;
                case static::STATE_NUMERIC:
                    $this->token_type = $hasDot ? static::FLOAT_ : static::INT_;
                    break;
                case static::STATE_HEXADECIMAL:
                    if ($this->pos - $pos <= 3) {
                        throw $this->unexpectedEndOfFormula();
                    }
                    else {
                        $this->token_type = static::HEXADECIMAL;
                    }
                    break;
                case static::STATE_BINARY:
                    if ($this->pos - $pos <= 3) {
                        throw $this->unexpectedEndOfFormula();
                    }
                    else {
                        $this->token_type = static::BINARY;
                    }
                    break;
                default:
                    throw $this->unexpectedEndOfFormula();
            }
        }

        $this->token_text = mb_substr($this->text, $pos, $this->pos - $pos);
        if ($this->token_type == static::IDENTIFIER) {
            $this->token_text = mb_strtolower($this->token_text);
            if (isset(static::$reserved_keywords[$this->token_text])) {
                $this->token_type = static::$reserved_keywords[$this->token_text];
            }
        }
    }

    protected function unexpectedCharacter(): UnexpectedCharacter
    {
        return new UnexpectedCharacter(
            __("Unexpected character ':ch'",
                ['ch' => $this->characters[$this->pos - 1]]),
            $this->text,
            $this->pos - 1,
            1
        );
    }

    protected function unexpectedEndOfFormula(): UnexpectedEOF
    {
        return new UnexpectedEOF(
            __("Unexpected end of formula"),
            $this->text,
            $this->length,
            1
        );
    }
    #endregion

    #region Parser

    public function parse(string $as = Formula::EXPR): Formula {
        $this->characters = preg_split('//u', $this->text, -1,
            PREG_SPLIT_NO_EMPTY);
        $this->length = mb_strlen($this->text);
        $this->parameter_count = count($this->parameters);
        $this->scan();

        $formula = $this->parseAs($as);

        if ($this->token_type != static::EOF) {
            throw $this->syntaxError(
                __("Expected end of formula, but ':token' found",
                    ['token' => $this->token_text]));
        }

        if ($this->parameter_index < $this->parameter_count - 1) {
            throw new InvalidParameters(__("Formula ':formula' expects less than :n parameters",
                ['formula' => $this->text, 'n' => $this->parameter_count]));
        }

        return $formula;
    }

    protected function parseAs(string $as): Formula
    {
        return match ($as) {
            Formula::SORT_EXPR =>
                $this->parseSortExpr(),
            Formula::SELECT_EXPR =>
                $this->parseSelectExpr(),
            Formula::IDENTIFIER, Formula::PARAMETER, Formula::CALL =>
                $this->parseSimple(),
            Formula::LOGICAL_OR, Formula::LOGICAL_XOR, Formula::LOGICAL_AND,
            Formula::BIT_OR, Formula::BIT_AND, Formula::BIT_SHIFT, Formula::ADD,
            Formula::MULTIPLY, Formula::BIT_XOR, Formula::COALESCE =>
                $this->parseOperator($as),
            Formula::LOGICAL_NOT =>
                $this->parseLogicalNot(),
            Formula::IS_NULL, Formula::IS_NOT_NULL, Formula::EQUAL,
            Formula::EQUAL_OR_GREATER, Formula::GREATER, Formula::EQUAL_OR_LESS,
            Formula::LESS, Formula::NOT_EQUAL, Formula::EQUAL_OR_NULL =>
                $this->parseBooleanPrimary(),
            Formula::NOT_IN, Formula::IN_, Formula::NOT_BETWEEN,
            Formula::BETWEEN, Formula::NOT_LIKE, Formula::LIKE,
            Formula::NOT_REGEXP, Formula::REGEXP =>
                $this->parsePredicate(),
            Formula::POSITIVE, Formula::NEGATIVE, Formula::BIT_INVERT =>
                $this->parseSignedSimple(),
            Formula::LITERAL =>
                $this->parseLiteral(),
            Formula::TERNARY =>
                $this->parseTernary(),
            default =>
                throw new NotSupported(__(
                    "Formula type ':type' not supported", ['type' => $as])),
        };
    }

    /**
     * select_expr ::= ternary [AS identifier]
     */
    protected function parseSelectExpr(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        $expr = $this->parseTernary();
        if ($this->token_type != static::AS_) {
            return $expr;
        }

        $this->scan();
        $this->expect(static::IDENTIFIER);
        $alias = $this->token_text;

        $this->scan();
        $length = $this->previous_pos - $pos;

        $result = Formula\SelectExpr::new(compact('expr',
            'alias', 'pos', 'formula', 'length'));
        $expr->parent = $result;

        return $result;
    }

    /**
     * sort_expr ::= ternary [ASC | DESC]
     */
    protected function parseSortExpr(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        $expr = $this->parseTernary();

        $ascending = true;
        switch ($this->token_type) {
            case static::ASC:
                $this->scan();
                break;
            case static::DESC:
                $ascending = false;
                $this->scan();
                break;
            default:
                return $expr;
        }

        $length = $this->previous_pos - $pos;

        $result = Formula\SortExpr::new(compact('expr',
            'ascending', 'pos', 'formula', 'length'));
        $expr->parent = $result;

        return $result;
    }

    /**
     * ternary ::= logical_or [ ? logical_or : logical_or ]
     */
    protected function parseTernary(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        $condition = $this->parseOperator(Formula::LOGICAL_OR);

        if ($this->token_type != static::QUESTION) {
            return $condition;
        }

        $this->scan();
        $then = $this->parseOperator(Formula::LOGICAL_OR);
        $this->expect(static::COLON);
        $this->scan();
        $else_ = $this->parseOperator(Formula::LOGICAL_OR);
        $length = $this->previous_pos - $pos;

        $result = Formula\Ternary::new(compact('condition',
            'then', 'else_', 'pos', 'formula', 'length'));
        $condition->parent = $result;
        $then->parent = $result;
        $else_->parent = $result;

        return $result;

    }

    /**
     * logical_or ::= logical_xor {OR logical_xor}
     * logical_xor ::= logical_and {XOR logical_and}
     * logical_and ::= logical_not {AND logical_not}
     * coalesce ::= bit_or {| bit_or}
     * bit_or ::= bit_and {| bit_and}
     * bit_and ::= bit_shift {| bit_shift}
     * bit_shift ::= add {( << | >> ) add}
     * add ::= multiply {( + | - ) multiply}
     * multiply ::= bit_xor {( * | / | DIV | MOD | % ) bit_xor}
     * bit_xor ::= signed_simple {^ signed_simple}
     */
    protected function parseOperator(string $type): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        /* @var Formula[] $operands */
        $operator = static::$operators[$type];
        $operands = [$this->parseAs($operator['operand'])];
        $operators = [];

        while (isset($operator['tokens'][$this->token_type])) {
            $operators[] = $this->token_type;
            $this->scan();
            $operands[] = $this->parseAs($operator['operand']);
        }

        if (count($operands) == 1) {
            return $operands[0];
        }

        $length = $this->previous_pos - $pos;
        $result = Formula\Operator::new(compact('type', 
            'operands', 'operators', 'pos', 'formula', 'length'));
            
        foreach ($operands as $operand) {
            $operand->parent = $result;
        }

        return $result;
    }

    /**
     * logical_not ::= NOT boolean_primary
     */
    protected function parseLogicalNot(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        if ($this->token_type != static::NOT) {
            return $this->parseBooleanPrimary();
        }

        $this->scan();
        $type = Formula::LOGICAL_NOT;
        $operand = $this->parseBooleanPrimary();
        $length = $this->previous_pos - $pos;
        $result = Formula\Unary::new(compact('type', 
            'operand', 'pos', 'formula', 'length'));
        $operand->parent = $result;
        return $result;
    }

    /**
     * boolean_primary ::=
     *      predicate IS [NOT] NULL |
     *      predicate ( = | >= | > | <= | < | <> | != ) predicate |
     *      predicate
     */
    protected function parseBooleanPrimary(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        $operand = $this->parsePredicate();

        if ($this->token_type == static::IS_) {
            $type = Formula::IS_NULL;
            $this->scan();

            if ($this->token_type == static::NOT) {
                $type = Formula::IS_NOT_NULL;
                $this->scan();
            }

            $this->expect(static::NULL_);
            $this->scan();

            $length = $this->previous_pos - $pos;
            $result = Formula\Unary::new(compact('type', 
                'operand', 'pos', 'formula', 'length'));
            $operand->parent = $result;
            return $result;
        }

        if (isset(static::$comparison_operators[$this->token_type])) {
            $type = static::$comparison_operators[$this->token_type];
            $operators = [$this->token_type];
            $operands = [$operand];

            $this->scan();
            $operands[] = $this->parsePredicate();
            $length = $this->previous_pos - $pos;
            $result = Formula\Operator::new(compact('type', 
                'operands', 'operators', 'pos',
                'formula', 'length'));
                
            foreach ($operands as $operand) {
                $operand->parent = $result;
            }
            return $result;
        }

        return $operand;
    }

    /**
     * predicate ::=
     *      coalesce [NOT] IN (simple {, simple}) |
     *      coalesce [NOT] BETWEEN coalesce AND coalesce |
     *      coalesce [NOT] LIKE simple |
     *      coalesce [NOT] REGEXP coalesce |
     *      coalesce
     */
    protected function parsePredicate(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        $value = $this->parseOperator(Formula::COALESCE);

        $isNegated = false;

        if ($this->token_type == static::NOT) {
            $isNegated = true;
            $this->scan();
        }

        if ($this->token_type == static::IN_) {
            $type = $isNegated ? Formula::NOT_IN : Formula::IN_;
            $this->scan();
            $this->expect(static::OPEN_PAR);
            $this->scan();

            $items = [$this->parseSimple()];

            while ($this->token_type == static::COMMA) {
                $this->scan();
                $items[] = $this->parseSimple();
            }

            $this->expect(static::CLOSE_PAR);
            $this->scan();
            $length = $this->previous_pos - $pos;
            $result = Formula\In_::new(compact('type', 
                'value', 'items', 'pos', 'formula', 'length'));
                
            $value->parent = $result;
            foreach ($items as $item) {
                $item->parent = $result;
            }

            return $result;
        }

        if ($this->token_type == static::BETWEEN) {
            $type = $isNegated ? Formula::NOT_BETWEEN : Formula::BETWEEN;
            $this->scan();
            $from = $this->parseOperator(Formula::COALESCE);

            $this->expect(static::AND_);
            $this->scan();

            $to = $this->parseOperator(Formula::COALESCE);
            $length = $this->previous_pos - $pos;

            $result = Formula\Between::new(compact('type', 
                'value', 'from', 'to', 'pos', 'formula',
                'length'));
                
            $value->parent = $result;
            $from->parent = $result;
            $to->parent = $result;
            
            return $result;
        }

        if ($this->token_type == static::LIKE) {
            $type = $isNegated ? Formula::NOT_LIKE : Formula::LIKE;
            $this->scan();
            $pattern = $this->parseSignedSimple();
            $length = $this->previous_pos - $pos;
            $result = Formula\Pattern::new(compact('type', 
                'value', 'pattern', 'pos', 'formula', 'length'));
                
            $value->parent = $result;
            $pattern->parent = $value;
            
            return $result;
        }

        if ($this->token_type == static::REGEXP) {
            $type = $isNegated ? Formula::NOT_REGEXP : Formula::REGEXP;
            $this->scan();
            $pattern = $this->parseOperator(Formula::COALESCE);
            $length = $this->previous_pos - $pos;
            $result = Formula\Pattern::new(compact('type', 
                'value', 'pattern', 'pos', 'formula', 'length'));
                
            $value->parent = $result;
            $pattern->parent = $value;
            
            return $result;
        }

        if ($isNegated) {
            throw $this->syntaxError(__(
                ":token1, :token2, :token3 or :token4 expected", [
                    'token1' => static::getTokenTitle(static::IN_),
                    'token2' => static::getTokenTitle(static::BETWEEN),
                    'token3' => static::getTokenTitle(static::LIKE),
                    'token4' => static::getTokenTitle(static::REGEXP),
                ]
            ));
        }

        return $value;
    }

    /**
     * signed_simple ::= [ + | - | ~ | ! ] simple
     */
    protected function parseSignedSimple(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        if (!isset(static::$signs[$this->token_type])) {
            return $this->parseSimple();
        }

        $type = static::$signs[$this->token_type];
        $this->scan();
        $operand = $this->parseSimple();
        $length = $this->previous_pos - $pos;
        $result = Formula\Unary::new(compact('type', 
            'operand', 'pos', 'formula', 'length'));
        $operand->parent = $result;
        return $result;
    }

    /**
     * simple ::=
     *      ( ternary ) |
     *      ? |
     *      identifier (expr {, expr}) |
     *      identifier {. identifier} |
     *      literal
     */
    protected function parseSimple(): Formula {
        $pos = $this->token_pos;
        $formula = $this->text;

        if ($this->token_type == static::OPEN_PAR) {
            $this->scan();
            $expr = $this->parseTernary();
            $this->expect(static::CLOSE_PAR);
            $this->scan();
            return $expr;
        }

        if ($this->token_type == static::QUESTION) {
            $this->scan();

            $index = $this->parameter_index++;

            $length = $this->previous_pos - $pos;
            return Formula\Parameter::new(compact('pos', 
                'formula', 'index', 'length'));
        }

        if ($this->token_type == static::IDENTIFIER) {
            $name = $this->token_text;
            $this->scan();
            if ($this->token_type == static::OPEN_PAR) {
                $this->scan();
                /* @var Formula[] $args */
                if ($this->token_type != static::CLOSE_PAR) {
                    $args = [$this->parseTernary()];
                    while ($this->token_type == static::COMMA) {
                        $this->scan();
                        $args[] = $this->parseTernary();
                    }
                }
                else {
                    $args = [];
                }
                $this->expect(static::CLOSE_PAR);
                $this->scan();
                $length = $this->previous_pos - $pos;
                $function = $name;
                $result = Formula\Call::new(compact('function', 
                    'args', 'pos', 'formula', 'length'));
                foreach ($args as $arg) {
                    $arg->parent = $result;
                }
                return $result;
            }

            $parts = [$name];
            while ($this->token_type == static::DOT) {
                $this->scan();
                $this->expect(static::IDENTIFIER);
                $parts[] = $this->token_text;
                $this->scan();
            }
            $length = $this->previous_pos - $pos;
            return Formula\Identifier::new(compact('parts', 
                'pos', 'formula', 'length'));
        }

        return $this->parseLiteral("Identifier or literal expected");
    }

    /**
     * literal ::=
     *      string  |
     *      numeric |
     *      hexadecimal |
     *      binary |
     *      TRUE |
     *      FALSE |
     *      NULL
     */
    protected function parseLiteral(string $error = "Literal expected")
        : Formula 
    {
        $pos = $this->token_pos;
        $formula = $this->text;

        if (isset($this->types->literals[$this->token_type])) {
            $token = $this->token_type;
            $value = $this->token_text;
            $this->scan();
            $length = $this->previous_pos - $pos;
            return Formula\Literal::new(compact('value', 
                'pos', 'formula', 'token', 'length'));
        }

        throw $this->syntaxError(__($error));
    }

    /**
     * @param int $tokenType
     * @throws SyntaxError
     */
    protected function expect(string $tokenType): void {
        if ($this->token_type != $tokenType) {
            throw $this->syntaxError(__(":token expected",
                ['token' => static::getTokenTitle($tokenType)]));
        }
    }

    protected function syntaxError($message): SyntaxError
    {
        return new SyntaxError($message, $this->text, $this->token_pos,
            $this->pos - $this->token_pos);
    }
    #endregion
}