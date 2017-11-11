<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 13:51
 */

namespace Modules\Category\Exceptions;

use Exception;

class CategoryException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "category" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }

}