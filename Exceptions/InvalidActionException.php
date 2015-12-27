<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        28.12.2015
 */
namespace BiberLtd\Bundle\LogBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class LoginException extends Services\ExceptionAdapter {
    public function __construct($kernel, $action = "", $code = 997000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested action '.$action.' is not registered with our database.',
            $code,
            $previous);
     }
}