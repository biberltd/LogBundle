<?php
/**
 * @vendor      BiberLtd
 * @package		Core\Bundles\LogBundle
 * @subpackage	Services
 * @name	    LogModel
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.3
 * @date        02.05.2015
 */
namespace BiberLtd\Bundle\LogBundle\Services;
/** Extends CoreModel */
use BiberLtd\Bundle\CoreBundle\CoreModel;

/** Entities to be used */
use BiberLtd\Bundle\LogBundle\Entity as BundleEntity;
use BiberLtd\Bundle\FileManagementBundle\Entity as FileBundleEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity as MLSEntity;
use BiberLtd\Bundle\SiteManagementBundle\Entity as SiteManagementEntity;

/** Helper Models */
use BiberLtd\Bundle\FileManagementBundle\Services as FMMService;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Services as MLSService;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\MemberManagementBundle\Services as MMMService;

/** Core Service*/
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use MyProject\Proxies\__CG__\stdClass;

class LogModel extends CoreModel {
    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.3
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine'){
        parent::__construct($kernel, $db_connection, $orm);

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
     * @name            __destruct()
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
     * @name 			countLogs()
     *  				Get the total count of logs.
     *
     * @since			1.0.1
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function countLogs($filter = null) {
        $timeStamp = time();
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

		return new ModelResponse($result, 1, 1, null, false, 'S:D:004', 'Entries have been counted successfully.', $timeStamp, time());
    }
    /**
     * @name 			deleteAction()
     *
     * @since			1.0.0
     * @version         1.0.3
	 *
     * @author          Can Berkol
     *
     * @use             $this->deleteActions()
     *
     * @param           mixed           $action
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteAction($action){
        return $this->deleteActions(array($action));
    }
    /**
     * @name 			deleteActions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
	public function deleteActions($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Action){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getAction($entry);
				if(!$response->error->exists){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }
    /**
     * @name 			deleteLog()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->deleteLogs()
     *
     * @param           mixed           $log
     *
     * @return          mixed           $response
     */
    public function deleteLog($log){
        return $this->deleteLogs(array($log));
    }
    /**
     * @name 			deleteLogs()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
	 *
     * @use             $this->createException()
     *
     * @param           array           $collection
	 *
     * @return          array           $response
     */
	public function deleteLogs($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Log){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getLog($entry);
				if(!$response->error->exists){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
	}
    /**
     * @name 			deleteSession()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->deleteSessions()
     *
     * @param           mixed           $session
     *
     * @return          mixed           $response
     */
    public function deleteSession($session){
        return $this->deleteSessions(array($session));
    }
    /**
     * @name 			deleteSessions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
    public function deleteSessions($collection){
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach($collection as $entry){
			if($entry instanceof BundleEntity\Session){
				$this->em->remove($entry);
				$countDeleted++;
			}
			else{
				$response = $this->getSession($entry);
				if(!$response->error->exists){
					$entry = $response->result->set;
					$this->em->remove($entry);
					$countDeleted++;
				}
			}
		}
		if($countDeleted < 0){
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
		}
		$this->em->flush();

		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }
    /**
     * @name 			doesActionExist()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->getAction()
     *
     * @param           mixed           $action         id, code
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
	public function doesActionExist($action, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getAction($action);

		if ($response->error->exists) {
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
		return new ModelResponse(true, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @name 			doesSessionExist()
	 *
	 * @since			1.0.0
	 * @version         1.0.3
	 * @author          Can Berkol
	 *
	 * @use             $this->getAction()
	 *
	 * @param           mixed           $session
	 * @param           bool            $bypass
	 *
	 * @return          mixed           $response
	 */
	public function doesSessionExist($session, $bypass = false) {
		$timeStamp = time();
		$exist = false;

		$response = $this->getSession($session);

		if ($response->error->exists) {
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

		return new ModelResponse(true, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			getAction()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           mixed           $action
     *
     * @return          mixed           $response
     */
	public function getAction($action) {
		$timeStamp = time();
		if($action instanceof BundleEntity\Action){
			return new ModelResponse($action, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
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
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			getLog()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @param           mixed			$log
     *
     * @return          mixed           $response
     */
	public function getLog($log) {
		$timeStamp = time();
		if($log instanceof BundleEntity\Log){
			return new ModelResponse($log, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch($log){
			case is_numeric($log):
				$result = $this->em->getRepository($this->entity['l']['name'])->findOneBy(array('id' => $log));
				break;
		}
		if(is_null($result)){
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			getSession()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           mixed           $session
     *
     * @return          mixed           $response
     */
	public function getSession($session) {
		$timeStamp = time();
		if($session instanceof BundleEntity\Session){
			return new ModelResponse($session, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
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
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			insertAction()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->insertActions()
     *
     * @param           mixed           $action               Entity or post
     *
     * @return          array           $response
     */
    public function insertAction($action){
        return $this->insertActions(array($action));
    }
    /**
     * @name 			insertActions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
	public function insertActions($collection)	{
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = array();
		$localizations = array();
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
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
    /**
     * @name 			insertLog()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->insertLogs()
     *
     * @param           mixed           $log               Entity or post
     *
     * @return          array           $response
     */
    public function insertLog($log){
        return $this->insertLogs(array($log));
    }
    /**
     * @name 			insertLogs()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
	public function insertLogs($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
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
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if(!$response->error->exists){
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
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
    /**
     * @name 			insertSession()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->insertSessions()
     *
     * @param           mixed           $session               Entity or post
     *
     * @return          array           $response
     */
    public function insertSession($session){
        return $this->insertSessions(array($session));
    }
    /**
     * @name 			insertSessions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
	public function insertSessions($collection) {
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
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
							if(!$response->error->exists){
								$entity->$set($response->result->set);
							}
							unset($response, $lModel);
							break;
						case 'site':
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if(!$response->error->exists){
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
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}
    /**
     * @name 			listActions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          array           $response
     */
    public function listActions($filter = null, $sortOrder = null, $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
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

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getAction()->getId();
            if(!isset($unique[$id])){
                $entities[] = $entry->getAction();
            }
        }
        $totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			listLogs()
	 *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          array           $response
     */
	public function listLogs($filter = null, $sortOrder = null, $limit = null){
		$timeStamp = time();
		if(!is_array($sortOrder) && !is_null($sortOrder)){
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
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
					case 'action':
					case 'site':
						$column = $this->entity['l']['alias'].'.'.$column;
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

		$entities = array();
		foreach($result as $entry){
			$id = $entry->getAction()->getId();
			if(!isset($unique[$id])){
				$entities[] = $entry->getAction();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			listLoggedActionsAdded()
     *
     * @since			1.0.2
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @uses            $this->listLogs()
     *
	 * @param           mixed 			$date
	 * @param           string 			$eq 		after, before, between, on
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          array           $response
     */
    public function listLoggedActionsAdded($date, $eq, $sortOrder = null, $limit = null) {
        $timeStamp = time();
        $eqOpts = array('after', 'before', 'between', 'on');
        if (!$date instanceof \DateTime && !is_array($date)) {
			return $this->createException('InvalidSortOrderException', '$date must be an instance of \DateTime or an array that holds exactly two instances of \DateTime', 'E:S:001');
        }
        if (!in_array($eq, $eqOpts)) {
			return $this->createException('InvalidSortOrderException', '$eq can have only one of the following values: "after", "before", "between", "on".', 'E:S:001');
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
		$response->stats->execution->end = time();
		return $response;
    }
    /**
     * @name 			listLoggedActionsAddedBetween()
     *
     * @since			1.0.2
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           \DateTime       $startDate
     * @param           \DateTime       $endDate
     *
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          array           $response
     */
    public function listLoggedActionsAddedBetween(\DateTime $startDate, \DateTime $endDate, $sortOrder = null, $limit = null){
        return $this->listLoggedActionsAdded(array($startDate, $endDate), 'between', $sortOrder, $limit);
    }
    /**
     * @name 			listSessions()
     *  				List sessions from database based on a variety of conditions.
     *
     * @since			1.0.0
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter
     * @param           array           $sortOrder
     * @param           array           $limit
     *
     * @return          array           $response
     */
    public function listSessions($filter = null, $sortOrder = null, $limit = null, $query_str = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';
        $where_str = '';
        $group_str = '';

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
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
    /**
     * @name 			updateAction()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->updateActions()
     *
     * @param           mixed           $action
     *
     * @return          mixed           $response
     */
    public function updateAction($action){
        return $this->updateActions(array($action));
    }
    /**
     * @name 			updateActions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
    public function updateActions($collection){
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
		$localizations = array();
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
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
	}
    /**
     * @name 			updateLog()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->updateLogs()
     *
     * @param           mixed           $log
     *
     * @return          mixed           $response
     */
    public function updateLog($log){
        return $this->updateLogs(array($log));
    }
    /**
     * @name 			updateLogs()
     *  				Updates one or more log details in database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
    public function updateLogs($collection){
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
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
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
	}
    /**
     * @name 			updateSession(
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->updateSessions()
     *
     * @param           mixed           $session
     *
     * @return          mixed           $response
     */
    public function updateSession($session){
        return $this->updateSessions(array($session));
    }
    /**
     * @name 			updateSessions()
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection
     *
     * @return          array           $response
     */
	public function updateSessions($collection){
		$timeStamp = time();
		/** Parameter must be an array */
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = array();
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
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }
}

/**
 * Change Log
 * **************************************
 * v1.0.3                      02.05.1015
 * Can Berkol
 * **************************************
 * CR :: Made compatible with CoreBundle v3.3
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 05.02.2014
 * **************************************
 * A listLoggedActionsAdded()
 * A listLoggedActionsAddedBetween()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 11.01.2014
 * **************************************
 * A countLogs()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 09.01.2014
 * **************************************
 * A __construct()
 * A __destruct()
 * A deleteAction()
 * A deleteActions()
 * A deleteLog()
 * A deleteLogs()
 * A deleteSession()
 * A deleteSessions()
 * A doesActionExist()
 * A doesSessionExist()
 * A getAction()
 * A getLog()
 * A getSession()
 * A insertAction()
 * A insertActions()
 * A insertLog()
 * A insertLogs()
 * A insertSession()
 * A insertSessions()
 * A listActions()
 * A listLogs()
 * A listSessions()
 * A updateAction()
 * A updateActions()
 * A updateLog()
 * A updateLogs()
 * A updateSession()
 * A updateSessions()
 */

