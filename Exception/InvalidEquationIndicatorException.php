<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        15.01.2016
 */
namespace BiberLtd\Bundle\LogBundle\Exception;

class InvalidEquationIndicatorException extends \Exception{
	public function __construct($eq){
		parent::__construct($eq.' is not a valid file type. Allowed values are: before, after, between, on.');
	}
}