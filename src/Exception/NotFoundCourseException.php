<?php

namespace App\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

class NotFoundCourseException extends Exception
{
    /**
     * NotFoundItemException constructor.
     */
    #[Pure] public function __construct($error)
    {
        parent::__construct($error);
    }
}