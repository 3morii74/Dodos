<?php

// app/Exceptions/ApiError.php
namespace App\Exceptions;

use Exception;

class ApiError extends Exception
{
    protected $status;

    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
        $this->status = $code >= 400 && $code < 500 ? 'fail' : 'error';
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusCode()
    {
        return $this->getCode();
    }
}