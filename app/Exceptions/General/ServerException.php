<?php

namespace App\Exceptions\General;

use Exception;

class ServerException extends Exception
{
    protected $code = 500;
    protected $message = 'Some Error Happened!';
}
