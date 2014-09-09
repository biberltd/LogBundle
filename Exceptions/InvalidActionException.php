<?php
/**
 * @name        InvalidActionException
 * @package		BiberLtd\Bundle\LogBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        01.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle cURL connection problems.
 *
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
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */