<?php
namespace BiberLtd\Core\Bundles\LogBundle\Entity;
/**
 * @name        log
 * @package		BiberLtd\Core\AccessManagementBundle
 *
 * @author		Murat Ünal
 * @version     1.0.1
 * @date        19.07.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="log",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_log_session_action", columns={"session","action"}),
 *         @ORM\Index(name="idx_n_log_date_action", columns={"date_action"}),
 *         @ORM\Index(name="idx_n_log_site_session_action", columns={"session","action","site"})
 *     }
 * )
 */
class Log extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $ip_v4;

    /** 
     * @ORM\Column(type="string", length=39, nullable=true)
     */
    private $ip_v6;

    /** 
     * @ORM\Column(type="text", nullable=false)
     */
    private $url;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    private $agent;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_action;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\LogBundle\Entity\Action", inversedBy="logs")
     * @ORM\JoinColumn(name="action", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $action;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\LogBundle\Entity\Session", inversedBy="logs")
     * @ORM\JoinColumn(name="session", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $session;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name                  setAction ()
     *                                  Sets the action property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $action
     *
     * @return          object                $this
     */
    public function setAction($action) {
        if(!$this->setModified('action', $action)->isModified()) {
            return $this;
        }
		$this->action = $action;
		return $this;
    }

    /**
     * @name            getAction ()
     *                            Returns the value of action property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->action
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @name                  setAgent ()
     *                                 Sets the agent property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $agent
     *
     * @return          object                $this
     */
    public function setAgent($agent) {
        if(!$this->setModified('agent', $agent)->isModified()) {
            return $this;
        }
		$this->agent = $agent;
		return $this;
    }

    /**
     * @name            getAgent ()
     *                           Returns the value of agent property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->agent
     */
    public function getAgent() {
        return $this->agent;
    }

    /**
     * @name                  setDateAction ()
     *                                      Sets the date_action property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_action
     *
     * @return          object                $this
     */
    public function setDateAction($date_action) {
        if(!$this->setModified('date_action', $date_action)->isModified()) {
            return $this;
        }
		$this->date_action = $date_action;
		return $this;
    }

    /**
     * @name            getDateAction ()
     *                                Returns the value of date_action property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_action
     */
    public function getDateAction() {
        return $this->date_action;
    }

    /**
     * @name                  setDetails ()
     *                                   Sets the details property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $details
     *
     * @return          object                $this
     */
    public function setDetails($details) {
        if(!$this->setModified('details', $details)->isModified()) {
            return $this;
        }
		$this->details = $details;
		return $this;
    }

    /**
     * @name            getDetails ()
     *                             Returns the value of details property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->details
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * @name                 setIpV4()
     *                            Sets the ip_v4 property.
     *                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $ip_v4
     *
     * @return          object                $this
     */
    public function setIpV4($ip_v4) {
        if(!$this->setModified('ip_v4', $ip_v4)->isModified()) {
            return $this;
        }
		$this->ip_v4 = $ip_v4;
		return $this;
    }

    /**
     * @name            getIpV4()
     *                      Returns the value of ip_v4 property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->ip_v4
     */
    public function getIpV4() {
        return $this->ip_v4;
    }

    /**
     * @name                  setIpV6()
     *                            Sets the ip_v6 property.
     *                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $ip_v6
     *
     * @return          object                $this
     */
    public function setIpV6($ip_v6) {
        if(!$this->setModified('ip_v6', $ip_v6)->isModified()) {
            return $this;
        }
		$this->ip_v6 = $ip_v6;
		return $this;
    }

    /**
     * @name            getIpV6()
     *                      Returns the value of ip_v6 property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->ip_v6
     */
    public function getIpV6() {
        return $this->ip_v6;
    }

    /**
     * @name                  setSession ()
     *                                   Sets the session property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $session
     *
     * @return          object                $this
     */
    public function setSession($session) {
        if(!$this->setModified('session', $session)->isModified()) {
            return $this;
        }
		$this->session = $session;
		return $this;
    }

    /**
     * @name            getSession ()
     *                             Returns the value of session property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->session
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * @name                  setSite ()
     *                                Sets the site property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $site
     *
     * @return          object                $this
     */
    public function setSite($site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @name            getSite ()
     *                          Returns the value of site property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->site
     */
    public function getSite() {
        return $this->site;
    }

    /**
     * @name                  setUrl ()
     *                               Sets the url property.
     *                               Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url
     *
     * @return          object                $this
     */
    public function setUrl($url) {
        if(!$this->setModified('url', $url)->isModified()) {
            return $this;
        }
		$this->url = $url;
		return $this;
    }

    /**
     * @name            getUrl ()
     *                         Returns the value of url property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url
     */
    public function getUrl() {
        return $this->url;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Murat Ünal
 * 19.07.2013
 * **************************************
 * A get_a()
 * A set_a()
 * A getAction()
 * A setAction()
 * A getAgent()
 * A setAgent()
 * A getDateAction()
 * A setDateAction()
 * A getDetails()
 * A setDetails()
 * A getId()
 * A setId()
 * A getIpV4()
 * A setIpV4()
 * A getIpV6()
 * A setIpV6()
 * A get_s()
 * A set_s()
 * A getSession()
 * A setSession()
 * A getSite()
 * A setSite()
 * A get_sn()
 * A set_sn()
 * A getUrl()
 * A setUrl()
 *
 */