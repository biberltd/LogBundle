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
 *     name="session",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_session_date_created", columns={"date_created"}),
 *         @ORM\Index(name="idx_n_session_date_access", columns={"date_access"}),
 *         @ORM\Index(name="idx_n_session_date_login", columns={"date_login"}),
 *         @ORM\Index(name="idx_n_session_date_logout", columns={"date_logout"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_session_id", columns={"id"})}
 * )
 */
class Session extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_access;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_login;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    public $date_logout;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $data;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $session_id;

	/**
	 * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
	 * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false)
	 * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Log", mappedBy="session")
     * @var array
     */
    private $logs;

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
	 * @param string $data
	 *
	 * @return $this
	 */
    public function setData(string $data) {
        if(!$this->setModified('data', $data)->isModified()) {
            return $this;
        }
		$this->data = $data;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getData() {
        return $this->data;
    }

	/**
	 * @param \DateTime $date_access
	 *
	 * @return $this
	 */
    public function setDateAccess(\DateTime $date_access) {
        if(!$this->setModified('date_access', $date_access)->isModified()) {
            return $this;
        }
		$this->date_access = $date_access;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateAccess() {
        return $this->date_access;
    }

	/**
	 * @param \DateTime $date_created
	 *
	 * @return $this
	 */
    public function setDateCreated(\DateTime $date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
		$this->date_created = $date_created;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateCreated() {
        return $this->date_created;
    }

	/**
	 * @param \DateTime $date_login
	 *
	 * @return $this
	 */
    public function setDateLogin(\DateTime $date_login) {
        if(!$this->setModified('date_login', $date_login)->isModified()) {
            return $this;
        }
		$this->date_login = $date_login;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateLogin() {
        return $this->date_login;
    }

	/**
	 * @param \DateTime $date_logout
	 *
	 * @return $this
	 */
    public function setDateLogout(\DateTime $date_logout) {
        if(!$this->setModified('date_logout', $date_logout)->isModified()) {
            return $this;
        }
		$this->date_logout = $date_logout;
		return $this;
    }

	/**
	 * @return \DateTime
	 */
    public function getDateLogout() {
        return $this->date_logout;
    }

	/**
	 * @param array $logs
	 *
	 * @return $this
	 */
    public function setLogs(array $logs) {
        if(!$this->setModified('logs', $logs)->isModified()) {
            return $this;
        }
		$this->logs = $logs;
		return $this;
    }

	/**
	 * @return array
	 */
    public function getLogs() {
        return $this->logs;
    }

	/**
	 * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
	 *
	 * @return $this
	 */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
	 */
    public function getMember() {
        return $this->member;
    }

	/**
	 * @param string $session_id
	 *
	 * @return $this
	 */
    public function setSessionId(string $session_id) {
        if(!$this->setModified('session_id', $session_id)->isModified()) {
            return $this;
        }
		$this->session_id = $session_id;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getSessionId() {
        return $this->session_id;
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
	 * @param string $username
	 *
	 * @return $this
	 */
    public function setUsername(string $username) {
        if(!$this->setModified('username', $username)->isModified()) {
            return $this;
        }
		$this->username = $username;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUsername() {
        return $this->username;
    }
}