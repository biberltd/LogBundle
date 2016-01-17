<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2016
 * @license     GPLv3
 *
 * @date        15.01.2016
 */
namespace BiberLtd\Bundle\LogBundle\Services;

use BiberLtd\Bundle\CoreBundle\CoreModel;

use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\LogBundle\Entity as BundleEntity;
use BiberLtd\Bundle\FileManagementBundle\Entity as FileBundleEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity as MLSEntity;
use BiberLtd\Bundle\SiteManagementBundle\Entity as SiteManagementEntity;
use BiberLtd\Bundle\FileManagementBundle\Services as FMMService;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Services as MLSService;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\MemberManagementBundle\Services as MMMService;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class LogModel extends CoreModel {
	/**
	 * LogModel constructor.
	 *
	 * @param object      $kernel
	 * @param string|null $dbConnection
	 * @param string|null $orm
	 */
	public function __construct($kernel, string $dbConnection = null, string $orm = null){
		parent::__construct($kernel, $dbConnection ?? 'default', $orm ?? 'doctrine');

		$this->entity = array(
			'a'		=> array('name' => 'LogBundle:Action', 'alias' => 'a'),
			'al' 	=> array('name' => 'LogBundle:ActionLocalization', 'alias' => 'al'),
			'l' 	=> array('name' => 'LogBundle:Log', 'alias' => 'l'),
			's' 	=> array('name' => 'LogBundle:Session', 'alias' => 's'),
			't' 	=> array('name' => 'SiteManagementBundle:Site', 'alias' => 't'),
			'm' 	=> array('name' => 'MemberManagementBundle:Member', 'alias' => 'm'),
		);
	}

	/**
	 * Destructor
	 */
	public function __destruct(){
		foreach($this as $property => $value) {
			$this->$property = null;
		}
	}

	/**
	 * @param array|null $filter
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function countLogs(array $filter = null) {
		$timeStamp = microtime(true);
		$wStr = '';

		$qStr = 'SELECT COUNT('. $this->entity['l']['alias'].')'
			.' FROM '.$this->entity['l']['name'].' '.$this->entity['l']['alias'];

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr;
		$q = $this->em->createQuery($qStr);

		$result = $q->getSingleScalarResult();

		return new ModelResponse($result, 1, 1, null, false, 'S:D:004', 'Entries have been counted successfully.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $action
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteAction($action){
		return $this->deleteActions(array($action));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteActions(array $collection) {
		$timeStamp = microtime(true);
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Action){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getAction($entry);
				if(!$response->error->exist){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $log
	 *
	 * @return array
	 */
	public function deleteLog($log){
		return $this->deleteLogs(array($log));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteLogs(array $collection) {
		$timeStamp = microtime(true);
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Log){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getLog($entry);
				if(!$response->error->exist){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $session
	 *
	 * @return array
	 */
	public function deleteSession($session){
		return $this->deleteSessions(array($session));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteSessions(array $collection){
		$timeStamp = microtime(true);
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Session){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getSession($entry);
				if(!$response->error->exist){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param           $action
	 * @param bool|null $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function doesActionExist($action, bool $bypass = null) {
		$bypass = $bypass ?? false;
		$timeStamp = microtime(true);
		$exist = false;

		$response = $this->getAction($action);

		if ($response->error->exist) {
			if($bypass){
				return $exist;
			}
			$response->result->set = false;
			return $response;
		}

		$exist = true;

		if ($bypass) {
			return $exist;
		}
		return new ModelResponse(true, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $session
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool|mixed
	 */
	public function doesSessionExist($session, bool $bypass = null) {
		$timeStamp = microtime(true);
		$bypass = $bypass ?? false;
		$exist = false;

		$response = $this->getSession($session);

		if ($response->error->exist) {
			if ($bypass) {
				return $exist;
			}
			$response->result->set = false;

			return $response;
		}

		$exist = true;

		if ($bypass) {
			return $exist;
		}

		return new ModelResponse(true, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $action
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getAction($action) {
		$timeStamp = microtime(true);
		if($action instanceof BundleEntity\Action){
			return new ModelResponse($action, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch($action){
			case is_numeric($action):
				$result = $this->em->getRepository($this->entity['a']['name'])->findOneBy(array('id' => $action));
				break;
			case is_string($action):
				$result = $this->em->getRepository($this->entity['a']['name'])->findOneBy(array('code' => $action));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $log
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getLog($log) {
		$timeStamp = microtime(true);
		if($log instanceof BundleEntity\Log){
			return new ModelResponse($log, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch($log){
			case is_numeric($log):
				$result = $this->em->getRepository($this->entity['l']['name'])->findOneBy(array('id' => $log));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $session
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getSession($session) {
		$timeStamp = microtime(true);
		if($session instanceof BundleEntity\Session){
			return new ModelResponse($session, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch($session){
			case is_numeric($session):
				$result = $this->em->getRepository($this->entity['s']['name'])->findOneBy(array('id' => $session));
				break;
			case is_string($session):
				$result = $this->em->getRepository($this->entity['s']['name'])->findOneBy(array('session_id' => $session));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $action
	 *
	 * @return array
	 */
	public function insertAction($action){
		return $this->insertActions(array($action));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertActionLocalizations(array $collection) {
		$timeStamp = microtime(true);
		$countInserts = 0;
		$insertedItems = [];
		foreach($collection as $data){
			if($data instanceof BundleEntity\ActionLocalization){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\ActionLocalization();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'language':
							$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
							$response = $lModel->getLanguage($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						case 'action':
							$response = $this->getAction($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
		}
		if($countInserts > 0){
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertActions(array $collection)	{
		$timeStamp = microtime(true);
		/** Parameter must be an array */
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = [];
		$localizations = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Action) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if (is_object($data)) {
				$entity = new BundleEntity\Action;
				if (!property_exists($data, 'date_added')) {
					$data->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (!property_exists($data, 'count_logs')) {
					$data->count_logs = 0;
				}
				foreach ($data as $column => $value) {
					$localeSet = false;
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							$localizations[$countInserts]['localizations'] = $value;
							$localeSet = true;
							$countLocalizations++;
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if (!$response->error->exist) {
								$entity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
					if ($localeSet) {
						$localizations[$countInserts]['entity'] = $entity;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
		}
		/** Now handle localizations */
		if ($countInserts > 0 && $countLocalizations > 0) {
			$response = $this->insertActionLocalizations($localizations);
		}
		if($countInserts > 0){
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $log
	 *
	 * @return array
	 */
	public function insertLog($log){
		return $this->insertLogs(array($log));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertLogs(array $collection) {
		$timeStamp = microtime(true);
		$countInserts = 0;
		$insertedItems = [];
		foreach($collection as $data){
			if($data instanceof BundleEntity\Log){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\Log();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'action':
							$response = $this->getAction($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						case 'session':
							$response = $this->getSession($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
		}
		if($countInserts > 0){
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $session
	 *
	 * @return array
	 */
	public function insertSession($session){
		return $this->insertSessions(array($session));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertSessions(array $collection) {
		$timeStamp = microtime(true);
		$countInserts = 0;
		$insertedItems = [];
		foreach($collection as $data){
			if($data instanceof BundleEntity\Session){
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
			else if(is_object($data)){
				$entity = new BundleEntity\Session();
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'member':
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if(!$response->error->exist){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			}
		}
		if($countInserts > 0){
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listActions(array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = microtime(true);
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['al']['alias'].', '.$this->entity['a']['alias']
			.' FROM '.$this->entity['al']['name'].' '.$this->entity['al']['alias']
			.' JOIN '.$this->entity['al']['alias'].'.action '.$this->entity['a']['alias'];

		if(!is_null($sortOrder)){
			foreach($sortOrder as $column => $direction){
				switch($column){
					case 'id':
					case 'code':
					case 'date_added':
					case 'date_updated':
					case 'date_removed':
					case 'count_logs':
					case 'type':
						$column = $this->entity['a']['alias'].'.'.$column;
						break;
					case 'name':
					case 'url_key':
						$column = $this->entity['al']['alias'].'.'.$column;
						break;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		if(!is_null($filter)){
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$entities = [];
		foreach($result as $entry){
			$id = $entry->getAction()->getId();
			if(!isset($unique[$id])){
				$entities[] = $entry->getAction();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param \nteger $count
	 * @param array   $filter
	 *
	 * @return array
	 */
	public function listRecentLogs(\nteger $count, array $filter = null){
		return $this->listLogs($filter, array('date_action' => 'desc'), array('start' => 0, 'count' => $count));
	}

	/**
	 * @param int   $count
	 * @param       $member
	 * @param array $filter
	 * @param array $sortOrder
	 * @param array $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listRecentLogsOfMember(int $count, $member, array $filter = [], array $sortOrder = [], $limit = []){
		return $this->listLogsOfMember($member, null, array('date_action' => 'desc'), array('start' => 0, 'count' => $count));
	}

	/**
	 * @param int   $count
	 * @param mixed $site
	 * @param array $filter
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listRecentLogsOfSite(int $count, $site, array $filter = null){
		$timeStamp = microtime(true);
		$sModel = new SMMService\SiteManagementModel($this->kernel, $this->dbConnection, $this->orm);
		$response = $sModel->getSite($site);
		if($response->error->exist){
			return $response;
		}
		$site = $response->result->set;
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['l']['alias'].'.site', 'comparison' => '=', 'value' => $site->getId()),
				)
			)
		);
		$response = $this->listLogs($filter, array('date_action' => 'desc'), array('start' => 0, 'count' => $count));
		$response->stats->execution->end = $timeStamp;
		return $response;
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listLogs(array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = microtime(true);
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['l']['alias'].', '.$this->entity['l']['alias']
			.' FROM '.$this->entity['l']['name'].' '.$this->entity['l']['alias'];

		if(!is_null($sortOrder)){
			foreach($sortOrder as $column => $direction){
				switch($column){
					case 'id':
					case 'ip_v4':
					case 'ip_v6':
					case 'url':
					case 'agent':
					case 'session':
					case 'date_action':
					case 'action':
					case 'site':
						$column = $this->entity['l']['alias'].'.'.$column;
						break;
					default:
						continue;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		if(!is_null($filter)){
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$entities = [];
		foreach($result as $entry){
			$id = $entry->getId();
			if(!isset($unique[$id])){
				$entities[] = $entry;
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listLogsOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = microtime(true);
		$mModel = new MMMService\MemberManagementModel($this->kernel, $this->dbConnection, $this->orm);
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;

		$timeStamp = microtime(true);

		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['s']['alias'].', '.$this->entity['l']['alias']
			.' FROM '.$this->entity['l']['name'].' '.$this->entity['l']['alias']
			.' JOIN '.$this->entity['l']['alias'].'.session '.$this->entity['s']['alias'];

		if(!is_null($sortOrder)){
			foreach($sortOrder as $column => $direction){
				switch($column){
					case 'id':
					case 'ip_v4':
					case 'ip_v6':
					case 'url':
					case 'agent':
					case 'session':
					case 'date_action':
					case 'action':
					case 'site':
						$column = $this->entity['l']['alias'].'.'.$column;
						break;
					default:
						continue;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['s']['alias'].'.member', 'comparison' => '=', 'value' => $member->getId()),
				)
			)
		);
		if(!is_null($filter)){
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$entities = [];
		foreach($result as $entry){
			$id = $entry->getId();
			if(!isset($unique[$id])){
				$entities[] = $entry;
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param \DateTime  $date
	 * @param string     $eq
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 * @throws \BiberLtd\Bundle\LogBundle\Services\InvalidEquationIndicatorException
	 */
	public function listLoggedActionsAdded(\DateTime $date, string $eq, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		$eqOpts = array('after', 'before', 'between', 'on');
		if (!in_array($eq, $eqOpts)) {
			throw new InvalidEquationIndicatorException($eq);
		}
		$column = $this->entity['l']['alias'].'.date_action';

		if ($eq == 'after' || $eq == 'before' || $eq == 'on') {
			switch ($eq) {
				case 'after':
					$eq = '>';
					break;
				case 'before':
					$eq = '<';
					break;
				case 'on':
					$eq = '=';
					break;
			}
			$condition = array('column' => $column, 'comparison' => $eq, 'value' => $date);
			$filter[] = array(
				'glue' => 'and',
				'condition' => array(
					array(
						'glue' => 'and',
						'condition' => $condition,
					)
				)
			);
		}
		else {
			$filter[] = array(
				'glue' => 'and',
				'condition' => array(
					array(
						'glue' => 'and',
						'condition' => array('column' => $column, 'comparison' => '>', 'value' => $date[0]),
					),
					array(
						'glue' => 'and',
						'condition' => array('column' => $column, 'comparison' => '<', 'value' => $date[1]),
					)
				)
			);
		}
		$response = $this->listLogs($filter, $sortOrder, $limit);
		$response->stats->execution->start = $timeStamp;
		$response->stats->execution->end = microtime(true);
		return $response;
	}

	/**
	 * @param \DateTime  $startDate
	 * @param \DateTime  $endDate
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listLoggedActionsAddedBetween(\DateTime $startDate, \DateTime $endDate, array $sortOrder = null, array $limit = null){
		return $this->listLoggedActionsAdded(array($startDate, $endDate), 'between', $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listSessions(array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = microtime(true);

		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['s']['alias']
			.' FROM '.$this->entity['s']['name'].' '.$this->entity['s']['alias'];

		if($sortOrder != null){
			foreach($sortOrder as $column => $direction){
				switch($column){
					case 'id':
					case 'session_id':
					case 'username':
					case 'date_created':
					case 'date_login':
					case 'date_logout':
					case 'date_access':
						$column = $this->entity['s']['alias'].'.'.$column;
						break;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		if($filter != null){
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}
		$qStr .= $wStr.$gStr.$oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $action
	 *
	 * @return array
	 */
	public function updateAction($action){
		return $this->updateActions(array($action));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateActions(array $collection){
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		$localizations = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Action) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			}
			else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" parameter and id parameter must have an integer value.', 'E:S:003');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				if (!property_exists($data, 'site')) {
					$data->site = 1;
				}
				$response = $this->getAction($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Action with id '.$data->id.' does not exist in database.', 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							foreach ($value as $langCode => $translation) {
								$localization = $oldEntity->getLocalization($langCode, true);
								$newLocalization = false;
								if (!$localization) {
									$newLocalization = true;
									$localization = new BundleEntity\ActionLocalization();
									$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
									$response = $mlsModel->getLanguage($langCode);
									$localization->setLanguage($response->result->set);
									$localization->setAction($oldEntity);
								}
								foreach ($translation as $transCol => $transVal) {
									$transSet = 'set' . $this->translateColumnName($transCol);
									$localization->$transSet($transVal);
								}
								if ($newLocalization) {
									$this->em->persist($localization);
								}
								$localizations[] = $localization;
							}
							$oldEntity->setLocalizations($localizations);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if($countUpdates > 0){
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $log
	 *
	 * @return array
	 */
	public function updateLog($log){
		return $this->updateLogs(array($log));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateLogs(array $collection){
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		foreach($collection as $data){
			if($data instanceof BundleEntity\Log){
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			}
			else if(is_object($data)){
				if(!property_exists($data, 'id') || !is_numeric($data->id)){
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" parameter and id parameter must have an integer value.', 'E:S:003');
				}
				if(!property_exists($data, 'date_action')){
					$data->date_action = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				$response = $this->getLog($data->id);
				if($response->error->exist){
					return $this->createException('EntityDoesNotExist', 'Log with id '.$data->id, 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'action':
							$response = $this->getAction($value);
							if (!$response->error) {
								$oldEntity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The action with the id / code "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value, 'id');
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if($oldEntity->isModified()){
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if($countUpdates > 0){
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $session
	 *
	 * @return array
	 */
	public function updateSession($session){
		return $this->updateSessions(array($session));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateSessions(array $collection){
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		foreach($collection as $data){
			if($data instanceof BundleEntity\Session){
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			}
			else if(is_object($data)){
				if(!property_exists($data, 'id') || !is_numeric($data->id)){
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" parameter and id parameter must have an integer value.', 'E:S:003');
				}
				if(!property_exists($data, 'date_access')){
					$data->date_access = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
				}
				$response = $this->getSession($data->id);
				if($response->error->exist){
					return $this->createException('EntityDoesNotExist', 'Session with id '.$data->id.' does not exist in database.', 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach($data as $column => $value){
					$set = 'set'.$this->translateColumnName($column);
					switch($column){
						case 'member':
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The member with the id / username / email "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if (!$response->error->exist) {
								$oldEntity->$set($response->result->set);
							} else {
								return $this->createException('EntityDoesNotExist', 'The site with the id / key / domain "'.$value.'" does not exist in database.', 'E:D:002');
							}
							unset($response, $sModel);
							break;
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if($oldEntity->isModified()){
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if($countUpdates > 0){
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}
}