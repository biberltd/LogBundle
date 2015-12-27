<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\LogBundle\Listeners;
use BiberLtd\Bundle\CoreBundle\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SessionLogger extends Core{
	public  $container;
	public 	$timezone;

    /**
     * SessionLogger constructor.
     *
     * @param       $container
     * @param       $kernel
     * @param array $db_options
     */
    public function __construct($container, $kernel, array $db_options = array('default', 'doctrine')){
        parent::__construct($kernel);
        $this->container = $container;
        $this->timezone = $kernel->getContainer()->getParameter('app_timezone');
    }

    /**
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $e
     */
    public function onKernelRequest(GetResponseEvent $e){
        $sm = $this->container->get('session_manager');
        $sm->register();
    }
}