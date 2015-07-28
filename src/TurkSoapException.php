<?php namespace mahbubcsedu\turksoap;
/**
 * Created by PhpStorm.
 * User: mahbub
 * Date: 7/28/15
 * Time: 2:32 AM
 */



use Exception;

class LaraturkException extends Exception {

    protected $errors;

    public function __construct($message, $errors = null, $code = 500, Exception $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }

}