<?php

namespace Osm\Admin\Queries\Exceptions;

class Error extends \Exception
{
    public function __construct(
        public string $actual_message,
        public string $formula,
        public int    $pos,
        public int    $length)
    {
        $message = $formula
            ? "$actual_message\n$formula\n" . str_repeat(' ', $pos) .
                str_repeat('-', $length)
            : $actual_message;
        parent::__construct($message);
    }
}