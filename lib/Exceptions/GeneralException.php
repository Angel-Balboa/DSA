<?php

namespace dsa\lib\Exceptions;

class GeneralException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function guardarLog()
    {
        // error_log($this->message . "\n",3,".log/temhum.log");
        echo $this->getMessage();
    }
}