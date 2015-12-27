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
namespace BiberLtd\Bundle\LogBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
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
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=15, nullable=true)
     * @var string
     */
    private $ip_v4;

    /** 
     * @ORM\Column(type="string", length=39, nullable=true)
     * @var string
     */
    private $ip_v6;

    /** 
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $url;

    /** 
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $agent;

    /** 
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $details;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_action;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Action", inversedBy="logs")
     * @ORM\JoinColumn(name="action", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\LogBundle\Entity\Action
     */
    private $action;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Session", inversedBy="logs")
     * @ORM\JoinColumn(name="session", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var  \BiberLtd\Bundle\LogBundle\Entity\Session
     */
    private $session;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

	/**
	 * @return mixed
	 */
    public function getId(){
        return $this->id;
    }

	/**
	 * @param \BiberLtd\Bundle\LogBundle\Entity\Action $action
	 *
	 * @return $this
	 */
    public function setAction(\BiberLtd\Bundle\LogBundle\Entity\Action $action) {
        if(!$this->setModified('action', $action)->isModified()) {
            return $this;
        }
		$this->action = $action;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\LogBundle\Entity\Action
	 */
    public function getAction() {
        return $this->action;
    }

	/**
	 * @param string $agent
	 *
	 * @return $this
	 */
    public function setAgent(\string $agent) {
        if(!$this->setModified('agent', $agent)->isModified()) {
            return $this;
        }
		$this->agent = $agent;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getAgent() {
        return $this->agent;
    }

	/**
	 * @param \DateTime $date_action
	 *
	 * @return $this
	 */
    public function setDateAction(\DateTime $date_action) {
        if(!$this->setModified('date_action', $date_action)->isModified()) {
            return $this;
        }
		$this->date_action = $date_action;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateAction() {
        return $this->date_action;
    }

	/**
	 * @param string $details
	 *
	 * @return $this
	 */
    public function setDetails(\string $details) {
        if(!$this->setModified('details', $details)->isModified()) {
            return $this;
        }
		$this->details = $details;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getDetails() {
        return $this->details;
    }

	/**
	 * @param string $ip_v4
	 *
	 * @return $this
	 */
    public function setIpV4(\string $ip_v4) {
        if(!$this->setModified('ip_v4', $ip_v4)->isModified()) {
            return $this;
        }
		$this->ip_v4 = $ip_v4;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getIpV4() {
        return $this->ip_v4;
    }

	/**
	 * @param string $ip_v6
	 *
	 * @return $this
	 */
    public function setIpV6(\string $ip_v6) {
        if(!$this->setModified('ip_v6', $ip_v6)->isModified()) {
            return $this;
        }
		$this->ip_v6 = $ip_v6;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getIpV6() {
        return $this->ip_v6;
    }

	/**
	 * @param \BiberLtd\Bundle\LogBundle\Entity\Session $session
	 *
	 * @return $this
	 */
    public function setSession(\BiberLtd\Bundle\LogBundle\Entity\Session $session) {
        if(!$this->setModified('session', $session)->isModified()) {
            return $this;
        }
		$this->session = $session;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\LogBundle\Entity\Session
	 */
    public function getSession() {
        return $this->session;
    }

	/**
	 * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
	 *
	 * @return $this
	 */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
	 */
    public function getSite() {
        return $this->site;
    }

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
    public function setUrl(\string $url) {
        if(!$this->setModified('url', $url)->isModified()) {
            return $this;
        }
		$this->url = $url;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUrl() {
        return $this->url;
    }
}