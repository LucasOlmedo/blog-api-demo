<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 15:33
 */

namespace Modules\Comment\Exceptions;

use Exception;

class CommentException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "comment" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }

}