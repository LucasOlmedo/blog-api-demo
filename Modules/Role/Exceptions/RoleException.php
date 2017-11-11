<?php
/**
 * Created by PhpStorm.
 * User: LucasOlmedo
 * Date: 13/08/2017
 * Time: 15:35
 */

namespace Modules\Role\Exceptions;

use Exception;

class RoleException extends Exception
{
    private $transMessage;
    /**
     * CategoryException constructor.
     * @param string $message
     */
    public function __construct($message)
    {
        $this->transMessage = "role" . "." . $message;
        parent::__construct(trans($this->transMessage));
    }
}