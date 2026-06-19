<?php

namespace App\Exceptions;

use Exception;

class AIAnalysisFailedException extends Exception
{
    public function __construct(string $message = 'AI analysis could not be completed.')
    {
        parent::__construct($message, 503);
    }
}
