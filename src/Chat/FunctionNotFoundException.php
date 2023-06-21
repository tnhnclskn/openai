<?php

namespace Tnhnclskn\OpenAI\Chat;

use Tnhnclskn\OpenAI\Exception;

class FunctionNotFoundException extends Exception
{
    public function __construct(string $function)
    {
        parent::__construct("Function not found: $function");
    }
}
