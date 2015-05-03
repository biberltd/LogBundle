<?php
namespace BiberLtd\Bundle\LogBundle\Entity;
/**
 * @name        action
 * @package		BiberLtd\Bundle\LogBundle
 *
 * @author		Can Berkol
 * @author		Murat Ünal
 * @version     1.0.3
 * @date        02.05.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 */
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="action",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb","temporary":false},
 *     indexes={
 *         @ORM\Index(name="idxNActionDateAdded", columns={"date_added"}),
 *         @ORM\Index(name="idxNActionType", columns={"type"}),
 *         @ORM\Index(name="idxNActionDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNActionDateRemoved", columns={"date_removed"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUActionId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUActionCode", columns={"code"})
 *     }
 * )
 */
class Action extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="smallint", length=5)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", unique=true, length=45, nullable=false)
     */
    private $code;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_logs;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $date_removed;

	/**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\LogBundle\Entity\ActionLocalization", mappedBy="action")
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Log", mappedBy="action")
     */
    private $logs;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
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
     * @name            setCode ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $code
     *
     * @return          object                $this
     */
    public function setCode($code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
		$this->code = $code;
		return $this;
    }

    /**
     * @name            getCode ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @name            setCountLogs ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_logs
     *
     * @return          object                $this
     */
    public function setCountLogs($count_logs) {
        if(!$this->setModified('count_logs', $count_logs)->isModified()) {
            return $this;
        }
		$this->count_logs = $count_logs;
		return $this;
    }

    /**
     * @name            getCountLogs ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_logs
     */
    public function getCountLogs() {
        return $this->count_logs;
    }


    /**
     * @name            setLogs ()
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
     * @name            setSite ()
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
     * @name            setType ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $type
     *
     * @return          object                $this
     */
    public function setType($type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
		$this->type = $type;
		return $this;
    }

    /**
     * @name            getType ()
	 *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->type
     */
    public function getType() {
        return $this->type;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.3                      02.05.2015
 * Can Berkol
 * **************************************
 * CR :: ORM updates.
 *
 * **************************************
 * v1.0.2                     Murat Ünal
 * 10.10.2013
 * **************************************
 * A getCode()
 * A getCountLogs()
 * A getDateAdded()
 * A getDateUpdated()
 * A getId()
 * A getLocalizations()
 * A getLogs()
 * A getSite()
 * A getType()
 *
 * A setCode()
 * A setCountLogs()
 * A set_date_added()
 * A setDateUpdated()
 * A setLocalizations()
 * A setLogs()
 * A setSite()
 * A setType()
 *
 */