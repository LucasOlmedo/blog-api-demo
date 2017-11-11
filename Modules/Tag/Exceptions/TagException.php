<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 15:35
 */

namespace Modules\Tag\Exceptions;

use Exception;

class TagException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "tag" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }
}