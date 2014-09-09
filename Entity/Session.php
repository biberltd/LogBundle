<?php
namespace BiberLtd\Core\Bundles\LogBundle\Entity;
/**
 * @name        session
 * @package		BiberLtd\Core\AccessManagementBundle
 *
 * @author		Murat Ünal
 * @version     1.0.2
 * @date        10.10.2013
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
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_access;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_login;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $date_logout;

    /**
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $data;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $session_id;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false)
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity="BiberLtd\Core\Bundles\LogBundle\Entity\Log", mappedBy="session")
     */
    private $logs;


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
     * @name                  setData ()
     *                                Sets the data property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $data
     *
     * @return          object                $this
     */
    public function setData($data) {
        if(!$this->setModified('data', $data)->isModified()) {
            return $this;
        }
		$this->data = $data;
		return $this;
    }

    /**
     * @name            getData ()
     *                          Returns the value of data property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @name                  setDateAccess ()
     *                                      Sets the date_access property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_access
     *
     * @return          object                $this
     */
    public function setDateAccess($date_access) {
        if(!$this->setModified('date_access', $date_access)->isModified()) {
            return $this;
        }
		$this->date_access = $date_access;
		return $this;
    }

    /**
     * @name            getDateAccess ()
     *                                Returns the value of date_access property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_access
     */
    public function getDateAccess() {
        return $this->date_access;
    }

    /**
     * @name                  setDateCreated ()
     *                                       Sets the date_created property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_created
     *
     * @return          object                $this
     */
    public function setDateCreated($date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
		$this->date_created = $date_created;
		return $this;
    }

    /**
     * @name            getDateCreated ()
     *                                 Returns the value of date_created property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_created
     */
    public function getDateCreated() {
        return $this->date_created;
    }

    /**
     * @name                  setDateLogin ()
     *                                     Sets the date_login property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_login
     *
     * @return          object                $this
     */
    public function setDateLogin($date_login) {
        if(!$this->setModified('date_login', $date_login)->isModified()) {
            return $this;
        }
		$this->date_login = $date_login;
		return $this;
    }

    /**
     * @name            getDateLogin ()
     *                               Returns the value of date_login property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_login
     */
    public function getDateLogin() {
        return $this->date_login;
    }

    /**
     * @name                  setDateLogout ()
     *                                      Sets the date_logout property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_logout
     *
     * @return          object                $this
     */
    public function setDateLogout($date_logout) {
        if(!$this->setModified('date_logout', $date_logout)->isModified()) {
            return $this;
        }
		$this->date_logout = $date_logout;
		return $this;
    }

    /**
     * @name            getDateLogout ()
     *                                Returns the value of date_logout property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_logout
     */
    public function getDateLogout() {
        return $this->date_logout;
    }

    /**
     * @name                  setLogs ()
     *                                Sets the logs property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $logs
     *
     * @return          object                $this
     */
    public function setLogs($logs) {
        if(!$this->setModified('logs', $logs)->isModified()) {
            return $this;
        }
		$this->logs = $logs;
		return $this;
    }

    /**
     * @name            getLogs ()
     *                          Returns the value of logs property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->logs
     */
    public function getLogs() {
        return $this->logs;
    }

    /**
     * @name                  setMember ()
     *                                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                            Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name                  setSessionId()
     *                                   Sets the session_id property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $session_id
     *
     * @return          object                $this
     */
    public function setSessionId($session_id) {
        if(!$this->setModified('session_id', $session_id)->isModified()) {
            return $this;
        }
		$this->session_id = $session_id;
		return $this;
    }

    /**
     * @name            getSessionId()
     *                             Returns the value of session_id property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->session_id
     */
    public function getSessionId() {
        return $this->session_id;
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
     * @name                  setUsername ()
     *                                    Sets the username property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $username
     *
     * @return          object                $this
     */
    public function setUsername($username) {
        if(!$this->setModified('username', $username)->isModified()) {
            return $this;
        }
		$this->username = $username;
		return $this;
    }

    /**
     * @name            getUsername ()
     *                              Returns the value of username property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->username
     */
    public function getUsername() {
        return $this->username;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.2                    Murat Ünal
 * 10.10.2013
 * **************************************
 * A get_date()
 * A getDateAccess()
 * A getDateCreated()
 * A getDateLogin()
 * A getDateLogout()
 * A getId()
 * A getLogs()
 * A getMember()
 * A getSessionId()
 * A getSite()
 * A setUsername()
 *
 * A set_date()
 * A setDateAccess()
 * A setDateCreated()
 * A setDateLogin()
 * A setDateLogout()
 * A set_date_logs()
 * A setMember()
 * A setSessionId()
 * A setSite()
 * A setUsername()
 *
 */