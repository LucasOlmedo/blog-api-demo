<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 15:34
 */

namespace Modules\Post\Exceptions;

use Exception;

class PostException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "post" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }
}