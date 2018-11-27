<?php

namespace Inkstation\Customer\Services;


class EmailVerificationResponse
{
    const DEFAULT_ERROR_MESSAGE = 'Your account activation failed. Please try again.';
    const DEFAULT_SUCCESS_MESSAGE = 'Your account has been updated with your new email address %s. Please log in using your new email address.';


    protected $message;
    protected $isSuccess;
    protected $errorCode = '';

    public static function success(string $newEmail, string $message = '')
    {
        $res = new self;
        $res->isSuccess = true;
        if ($message === '') {
            $res->message = sprintf(self::DEFAULT_SUCCESS_MESSAGE, $newEmail);
        }
        return $res;
    }

    public static function error(string $errorCode, string $message = '')
    {
        $res = new self;
        $res->errorCode = $errorCode;
        $res->isSuccess = false;

        if ($message === '') {
            $res->message = self::DEFAULT_ERROR_MESSAGE . ' [' . $errorCode . ']';
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return boolean
     */
    public function getIsSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->isSuccess;
    }

}