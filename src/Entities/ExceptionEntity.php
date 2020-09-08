<?php

namespace ZnLib\Rest\Entities;

class ExceptionEntity
{

    public $message;
    public $code;
    public $status;
    public $type;
    public $file;
    public $line;
    public $trace;
    public $previous;

}