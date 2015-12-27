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
 * @ORM\Entity(repositoryClass="action_localization")
 * @ORM\Table(
 *     name="action_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_action_name", columns={"language","name"}),
 *         @ORM\UniqueConstraint(name="idx_u_action_url_key", columns={"action","language","url_key"}),
 *         @ORM\UniqueConstraint(name="idx_u_action_localization", columns={"action","language"})
 *     }
 * )
 */
class ActionLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $url_key;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $description;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Action", inversedBy="localizations")
     * @ORM\JoinColumn(name="action", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @ORM\Id
     * @var \BiberLtd\Bundle\LogBundle\Entity\Action
     */
    private $action;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @ORM\Id
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

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
	 * @param string $description
	 *
	 * @return $this
	 */
    public function setDescription(\string $description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
		$this->description = $description;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getDescription() {
        return $this->description;
    }

	/**
	 * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
	 *
	 * @return $this
	 */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

	/**
	 * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
	 */
    public function getLanguage() {
        return $this->language;
    }

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
    public function setName(\string $name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getName() {
        return $this->name;
    }

	/**
	 * @param string $url_key
	 *
	 * @return $this
	 */
    public function setUrlKey(\string $url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
		$this->url_key = $url_key;
		return $this;
    }

	/**
	 * @return string
	 */
    public function getUrlKey() {
        return $this->url_key;
    }
}