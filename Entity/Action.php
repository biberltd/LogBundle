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
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="string", unique=true, length=45, nullable=false)
     * @var string
     */
    private $code;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /** 
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"v"})
     * @var string
     */
    private $type;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false, options={"default":0})
     * @var int
     */
    private $count_logs;

	/**
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\LogBundle\Entity\ActionLocalization", mappedBy="action")
	 * @var array
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Log", mappedBy="action")
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
	 * @param string $code
	 *
	 * @return $this
	 */
    public function setCode(string $code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
		$this->code = $code;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getCode() {
        return $this->code;
    }

	/**
	 * @param int $count_logs
	 *
	 * @return $this
	 */
    public function setCountLogs(int $count_logs) {
        if(!$this->setModified('count_logs', $count_logs)->isModified()) {
            return $this;
        }
		$this->count_logs = $count_logs;
		return $this;
    }

	/**
	 * @return int
	 */
    public function getCountLogs() {
        return $this->count_logs;
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
	 * @param string $type
	 *
	 * @return $this
	 */
    public function setType(string $type) {
        if(!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
		$this->type = $type;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getType() {
        return $this->type;
    }
}