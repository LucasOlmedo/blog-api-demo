<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 15:36
 */

namespace Modules\User\Exceptions;

use Exception;

class UserException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "user" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }
}