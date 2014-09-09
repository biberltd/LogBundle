<?php
namespace BiberLtd\Bundle\LogBundle\Entity;
/**
 * @name        action
 * @package		BiberLtd\Bundle\LogBundle
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
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="action",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb","temporary":false},
 *     indexes={
 *         @ORM\Index(name="idx_n_action_date_added", columns={"date_added"}),
 *         @ORM\Index(name="idx_n_action_type", columns={"type"}),
 *         @ORM\Index(name="idx_n_action_date_updated", columns={"date_updated"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_action_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_action_code", columns={"code"})
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
     * @name                  setCode ()
     *                                Sets the code property.
     *                                Updates the data only if stored value and value to be set are different.
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
     *                          Returns the value of code property.
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
     * @name                  setCountLogs ()
     *                                     Sets the count_logs property.
     *                                     Updates the data only if stored value and value to be set are different.
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
     *                               Returns the value of count_logs property.
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
     * @name                  setType ()
     *                                Sets the type property.
     *                                Updates the data only if stored value and value to be set are different.
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
     *                          Returns the value of type property.
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