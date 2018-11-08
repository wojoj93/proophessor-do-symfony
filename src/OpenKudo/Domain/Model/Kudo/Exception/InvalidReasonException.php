<?php

namespace OpenKudo\Domain\Model\Kudo\Exception;

use Throwable;

final class InvalidReasonException extends \InvalidArgumentException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Reason field: '.$message;
        parent::__construct($message, $code, $previous);
    }
}
