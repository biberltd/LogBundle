<?php
/**
 * @vendor      BiberLtd
 * @package		LogBundle
 * @subpackage	Services
 * @name	    SessionLogger
 *
 * @author		Can Berkol
 *
 * @version     1.0.0
 * @date        26.04.2015
 *
 */

namespace BiberLtd\Bundle\LogBundle\Listeners;
use BiberLtd\Bundle\CoreBundle\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SessionLogger extends Core{
    private     $container;
	private 	$timezone;
    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object      $container
     * @param           object      $kernel
     * @param           array       $db_options
     */
    public function __construct($container, $kernel, $db_options = array('default', 'doctrine')){
        parent::__construct($kernel);
        $this->container = $container;
        $this->timezone = $kernel->getContainer()->getParameter('app_timezone');
    }
    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name 			onKernelRequest()
     *  				Called onKernelRequest event and registers a new session if necessary.
     *
     * @author          Can Berkol
     *
     * @use             BiberLtd\Bundle\CoreBundleServices\SessionManager
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @param 			GetResponseEvent 	        $e
     *
     */
    public function onKernelRequest(GetResponseEvent $e){
        $sm = $this->container->get('session_manager');
        $sm->register();
    }
}
/**
 * Change Log
 * ****************************************
 * v1.0.0						26.04.2015
 * TW #
 * Can Berkol
 * ****************************************
 * - Class moved to LogBundle from CoreBundle.
 */