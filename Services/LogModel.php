<?php
/**
 * LogModel Class
 *
 * This class acts as a database proxy model for LogModel functionalities.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\LogBundle
 * @subpackage	Services
 * @name	    LogModel
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.2
 * @date        05.07.2014
 *
 * =============================================================================================================
 * !! INSTRUCTIONS ON IMPORTANT ASPECTS OF MODEL METHODS !!!
 *
 * Each model function must return a $response ARRAY.
 * The array must contain the following keys and corresponding values.
 *
 * $response = array(
 *              'result'    =>   An array that contains the following keys:
 *                               'set'         Actual result set returned from ORM or null
 *                               'total_rows'  0 or number of total rows
 *                               'last_insert_id' The id of the item that is added last (if insert action)
 *              'error'     =>   true if there is an error; false if there is none.
 *              'code'      =>   null or a semantic and short English string that defines the error concanated
 *                               with dots, prefixed with err and the initials of the name of model class.
 *                               EXAMPLE: err.amm.action.not.found success messages have a prefix called scc..
 *
 *                               NOTE: DO NOT FORGET TO ADD AN ENTRY FOR ERROR CODE IN BUNDLE'S
 *                               RESOURCES/TRANSLATIONS FOLDER FOR EACH LANGUAGE.
 * =============================================================================================================
 * TODOs:
 * Do not forget to implement SITE, ORDER, AND PAGINATION RELATED FUNCTIONALITY
 *
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
     * @version         1.0.9
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine'){
        parent::__construct($kernel, $db_connection, $orm);

        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'action'    => array('name' => 'LogBundle:Action', 'alias' => 'a'),
            'action_localization' => array('name' => 'LogBundle:ActionLocalization', 'alias' => 'al'),
            'log' => array('name' => 'LogBundle:Log', 'alias' => 'l'),
            'session' => array('name' => 'LogBundle:Session', 'alias' => 's'),
            'site' => array('name' => 'SiteManagementBundle:Site', 'alias' => 't'),
            'member' => array('name' => 'MemberManagementBundle:Member', 'alias' => 'm'),
        );
    }
    /**
     * @name            __destruct()
     *                  Destructor.
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
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     * @param           string          $query_str              Custom query
     *
     * @return          array           $response
     */
    public function countLogs($filter = null, $query_str = null) {
        $this->resetResponse();
        /**
         * Add filter checks to below to set join_needed to true.
         */
        $where_str = '';
        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT COUNT('. $this->entity['log']['alias'].')'
                .' FROM '.$this->entity['log']['name'].' '.$this->entity['log']['alias'];
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $query_str .= $where_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare & Return Response
         */
        $result = $query->getSingleScalarResult();

        $this->response = array(
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			deleteAction()
     *  				Deletes an existing action from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->deleteActions()
     *
     * @param           mixed           $data             a single value of 'entity', 'id', 'url_key'
     * @param           string          $by               'entity', 'id', 'url_key'
     *
     * @return          mixed           $response
     */
    public function deleteAction($data, $by = 'entity'){
        return $this->deleteActions(array($data), $by);
    }
    /**
     * @name 			deleteActions()
     *  				Deletes provided actions from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following: 'entity', 'id', 'sku', 'site', 'type', 'status'
     * @param           string          $by             Accepts the following options: 'entity', 'id', 'code' 'type', 'site'
     *
     * @return          array           $response
     */
    public function deleteActions($collection, $by = 'entity'){
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'code', 'site', 'type');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.value');
        }
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if($by == 'entity'){
            $sub_response = $this->delete_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Action');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.deleted.done',
                );

                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $tih-s>prepare_delete.
         */
        $table = $this->entity['action']['name'].' '.$this->entity['action']['alias'];
        $q_str = $this->prepare_delete($table, $this->entity['action']['alias'].'.'.$by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'result'     => array(
                'set'           => $collection,
                'total_rows'    => $collection_count,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.deleted.done',
        );
        return $this->response;
    }
    /**
     * @name 			deleteLog()
     *  				Deletes an existing log entry from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->deleteLogs()
     *
     * @param           mixed           $data             a single value of 'entity', 'id', 'url_key'
     * @param           string          $by               'entity', 'id', 'url_key'
     *
     * @return          mixed           $response
     */
    public function deleteLog($data, $by = 'entity'){
        return $this->deleteLogs(array($data), $by);
    }
    /**
     * @name 			deleteLogs()
     *  				Deletes provided logs from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection.
     * @param           string          $by             Accepts the following options: 'entity', 'id', 'session' 'action', 'site'
     *
     * @return          array           $response
     */
    public function deletelogs($collection, $by = 'entity'){
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'session', 'action', 'site');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.value');
        }
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if($by == 'entity'){
            $sub_response = $this->delete_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Log');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.deleted.done',
                );

                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $tih-s>prepare_delete.
         */
        $table = $this->entity['log']['name'].' '.$this->entity['log']['alias'];
        $q_str = $this->prepare_delete($table, $this->entity['log']['alias'].'.'.$by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'result'     => array(
                'set'           => $collection,
                'total_rows'    => $collection_count,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.deleted.done',
        );
        return $this->response;
    }
    /**
     * @name 			deleteSession()
     *  				Deletes an existing session from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->deleteSessions()
     *
     * @param           mixed           $data
     * @param           string          $by               'entity', 'id', 'session_id'
     *
     * @return          mixed           $response
     */
    public function deleteSession($data, $by = 'entity'){
        return $this->deleteSessions(array($data), $by);
    }
    /**
     * @name 			deleteActions()
     *  				Deletes provided actions from database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection
     * @param           string          $by             Accepts the following options: 'entity', 'id', 'session_id' 'member', 'site'
     *
     * @return          array           $response
     */
    public function deleteSessions($collection, $by = 'entity'){
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'session_id', 'site', 'member');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.value');
        }
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if($by == 'entity'){
            $sub_response = $this->delete_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Session');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.deleted.done',
                );

                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $tih-s>prepare_delete.
         */
        $table = $this->entity['session']['name'].' '.$this->entity['session']['alias'];
        $q_str = $this->prepare_delete($table, $this->entity['session']['alias'].'.'.$by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'result'     => array(
                'set'           => $collection,
                'total_rows'    => $collection_count,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.deleted.done',
        );
        return $this->response;
    }
    /**
     * @name 			doesActionExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->getAction()
     *
     * @param           mixed           $action         id, code
     * @param           string          $by             all, entity, id, code, url_key
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesActionExist($action, $by = 'id', $bypass = false){
        $this->resetResponse();
        $exist = false;

        $response = $this->getAction($action, $by);

        if(!$response['error'] && $response['result']['total_rows'] > 0){
            $exist = true;
        }
        if($bypass){
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $exist,
                'total_rows'    => 1,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			doesSectionExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->getAction()
     *
     * @param           mixed           $action         id, code
     * @param           string          $by             all, entity, id, code, url_key
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesSessionExist($action, $by = 'id', $bypass = false){
        $this->resetResponse();
        $exist = false;

        $response = $this->getSession($action, $by);

        if(!$response['error'] && $response['result']['total_rows'] > 0){
            $exist = true;
        }
        if($bypass){
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $exist,
                'total_rows'    => 1,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			getAction()
     *  				Returns details of an action.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listActions()
     *
     * @param           mixed           $action             id, code
     * @param           string          $by                 entity, id, code
     *
     * @return          mixed           $response
     */
    public function getAction($action, $by = 'id'){
        $this->resetResponse();
        $by_opts = array('id', 'code', 'entity');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if(!is_object($action) && !is_numeric($action) && !is_string($action)){
            return $this->createException('InvalidParameterException', 'Action', 'err.invalid.parameter.action');
        }
        if(is_object($action)){
            if(!$action instanceof BundleEntity\Action){
                return $this->createException('InvalidParameterException', 'Action', 'err.invalid.parameter.action');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'result'     => array(
                    'set'           => $action,
                    'total_rows'    => 1,
                    'last_insert_id'=> null,
                ),
                'error'      => false,
                'code'       => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['action']['alias'].'.'.$by, 'comparison' => '=', 'value' => $action),
                )
            )
        );

        $response = $this->listActions($filter, null, array('start' => 0, 'count' => 1));
        if($response['error']){
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $collection[0],
                'total_rows'    => 1,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			getLog()
     *  				Returns details of a log.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listLogs()
     *
     * @param           mixed           $log             id,
     * @param           string          $by              entity, id
     *
     * @return          mixed           $response
     */
    public function getLog($log, $by = 'id'){
        $this->resetResponse();
        $by_opts = array('id', 'entity');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if(!is_object($log) && !is_numeric($log) && !is_string($log)){
            return $this->createException('InvalidParameterException', 'Log', 'err.invalid.parameter.log');
        }
        if(is_object($log)){
            if(!$action instanceof BundleEntity\Log){
                return $this->createException('InvalidParameterException', 'Log', 'err.invalid.parameter.log');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'result'     => array(
                    'set'           => $action,
                    'total_rows'    => 1,
                    'last_insert_id'=> null,
                ),
                'error'      => false,
                'code'       => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['log']['alias'].'.'.$by, 'comparison' => '=', 'value' => $log),
                )
            )
        );

        $response = $this->listLogs($filter, null, array('start' => 0, 'count' => 1));
        if($response['error']){
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $collection[0],
                'total_rows'    => 1,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			getSession()
     *  				Returns details of a session.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listLogs()
     *
     * @param           mixed           $session             id, session_id
     * @param           string          $by              entity, id
     *
     * @return          mixed           $response
     */
    public function getSession($session, $by = 'id'){
        $this->resetResponse();
        $by_opts = array('id', 'entity', 'session_id');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if(!is_object($session) && !is_numeric($session) && !is_string($session)){
            return $this->createException('InvalidParameterException', 'Session', 'err.invalid.parameter.session');
        }
        if(is_object($session)){
            if(!$session instanceof BundleEntity\Session){
                return $this->createException('InvalidParameterException', 'Session', 'err.invalid.parameter.session');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'result'     => array(
                    'set'           => $session,
                    'total_rows'    => 1,
                    'last_insert_id'=> null,
                ),
                'error'      => false,
                'code'       => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['session']['alias'].'.'.$by, 'comparison' => '=', 'value' => $session),
                )
            )
        );

        $response = $this->listSessions($filter, null, array('start' => 0, 'count' => 1));
        if($response['error']){
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $collection[0],
                'total_rows'    => 1,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			insertAction()
     *  				Inserts one action into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->insertActions()
     *
     * @param           mixed           $action               Entity or post
     *
     * @return          array           $response
     */
    public function insertAction($action){
        $this->resetResponse();
        return $this->insertActions(array($action));
    }
    /**
     * @name 			insertActions()
     *  				Inserts one or more actions into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertActions($collection, $by = 'post'){
        $this->resetResponse();

        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        if($by == 'entity'){
            $sub_response = $this->insert_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Action');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.insert.done.',
                );

                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $l_collection = array();
        $to_insert = 0;
        foreach($collection as $item){
            $localizations = array();
            if(isset($item['localizations'])){
                $localizations = $item['localizations'];
                unset($item['localizations']);
            }
            $site = '';
            if(isset($item['site'])){
                $site = $item['site'];
                unset($item['site']);
            }
            $entity = new BundleEntity\Action();
            foreach($item as $column => $value){
                $method = 'set_'.$column;
                if(method_exists($entity, $method)){
                    $entity->$method($value);
                }
            }
            /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
            if(count($localizations) > 0 ){
                $l_collection[] = $localizations;
            }

            /** HANDLE FOREIGN DATA :: SITE */
            if(!is_numeric($site)){
                $SMModel = new SMMService\SiteManagementModel($this->kernel, $this->db_connection, $this->orm);
                $response = $SMModel->getSite($value, 'id');
                if($response['error']){
                    new CoreExceptions\InvalidSiteException($this->kernel, $value);
                    break;
                }
                $site = $response['result']['set'];
                $entity->$method($site);
                /** Free up some memory */
                unset($site, $response, $SMModel);
            }
            $this->insert_entities(array($entity), '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Action');

            $entity_localizations = array();
            foreach($l_collection as $localization){
                if($localization instanceof BundleEntity\ActionLocalization){
                    $entity_localizations[] = $localization;
                }
                else{
                    $localization_entity = new BundleEntity\ActionLocalization;
                    $localization_entity->setAction($entity);
                    foreach($localization as $key => $value){
                        $l_method = 'set_'.$key;
                        switch($key){
                            case 'language';
                                $MLSModel = new MLSService\MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);
                                $response = $MLSModel->getLanguage($value, 'id');
                                if($response['error']){
                                    new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                                    break;
                                }
                                $language = $response['result']['set'];
                                $localization_entity->setLanguage($language);
                                unset($response, $MLSModel);
                                break;
                            default:
                                if(method_exists($localization_entity, $l_method)){
                                    $localization_entity->$l_method($value);
                                }
                                else{
                                    new CoreExceptions\InvalidMethodException($this->kernel, $method);
                                }
                                break;
                        }
                        $collection[] = $localization_entity;
                    }
                }
            }
            $this->insert_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\ActionLocalization');
            /**
             * ????? DO we really need this?
             *
             * Test! Also check if you can make use of insert_localizations functions but of course this will require
             * dependency on MLSModel.
             */
            $entity->setLocalizations($entity_localizations);
            $this->em->persist($entity);
            $to_insert++;
            /** Free some memory */
            unset($entity_localizations);
        }
        $this->em->flush();
        $code = 'scc.db.insert.done';
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $collection,
                'total_rows'    => $to_insert,
                'last_insert_id'=> $entity->getId(),
            ),
            'error'      => false,
            'code'       => $code,
        );
        return $this->response;
    }
    /**
     * @name 			insertLog()
     *  				Inserts one log entry into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->insertLogs()
     *
     * @param           mixed           $log               Entity or post
     *
     * @return          array           $response
     */
    public function insertLog($log){
        $this->resetResponse();
        return $this->insertLogs(array($log));
    }
    /**
     * @name 			insertLogs()
     *  				Inserts one or more logs into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertLogs($collection, $by = 'post'){
        $this->resetResponse();

        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        if($by == 'entity'){
            $sub_response = $this->insert_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Log');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.insert.done.',
                );

                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $to_insert = 0;
        foreach($collection as $item){
            $site = null;
            if(isset($item['site'])){
                $site = $item['site'];
                unset($item['site']);
            }
            $session = null;
            if(isset($item['session'])){
                $session = $item['session'];
                unset($item['session']);
            }
            $action = null;
            if(isset($item['action'])){
                $action = $item['action'];
                unset($item['action']);
            }
            $entity = new BundleEntity\Log();
            foreach($item as $column => $value){
                $method = 'set'.$this->translateColumnName($column);
                if(method_exists($entity, $method)){
                    $entity->$method($value);
                }
            }
            /** HANDLE FOREIGN DATA :: SITE */
            if(!is_null($site) && is_numeric($site)){
                $SMModel = new SMMService\SiteManagementModel($this->kernel, $this->db_connection, $this->orm);
                $response = $SMModel->getSite($site, 'id');
                if($response['error']){
                    new CoreExceptions\InvalidSiteException($this->kernel, $value);
                    break;
                }
                $site = $response['result']['set'];
                /** Free up some memory */
                unset($response, $SMModel);
            }
            $entity->setSite($site);
            /** HANDLE FOREIGN DATA :: ACTION */
            if(!is_null($action) && (is_numeric($action) || is_string($action))){
                if(is_numeric($action)){
                    $response = $this->getAction($action, 'id');
                }
                else if(is_string($action)){
                    $response = $this->getAction($action, 'code');
                }
                if($response['error']){
                    new CoreExceptions\EntityDoesNotExistException($this->kernel, $action);
                    break;
                }
                $action = $response['result']['set'];
                /** Free up some memory */
                unset($response);
            }
            $entity->setAction($action);
            /** HANDLE FOREIGN DATA :: SESSION */
            if(!is_null($session) && is_numeric($session)){
                $response = $this->getSession($session, 'id');
                if($response['error']){
                    new CoreExceptions\InvalidSiteException($this->kernel, $value);
                    break;
                }
                $session = $response['result']['set'];
                $entity->$method($session);
                /** Free up some memory */
                unset($response);
            }

            $entity->setSession($session);
            $this->insert_entities(array($entity), '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Log');

            /**
             * ????? DO we really need this?
             *
             * Test! Also check if you can make use of insert_localizations functions but of course this will require
             * dependency on MLSModel.
             */
            $this->em->persist($entity);
            $to_insert++;
        }
        $this->em->flush();
        $code = 'scc.db.insert.done';
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $collection,
                'total_rows'    => $to_insert,
                'last_insert_id'=> $entity->getId(),
            ),
            'error'      => false,
            'code'       => $code,
        );
        return $this->response;
    }
    /**
     * @name 			insertSession()
     *  				Inserts one session entry into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->insertSessions()
     *
     * @param           mixed           $session               Entity or post
     *
     * @return          array           $response
     */
    public function insertSession($session){
        $this->resetResponse();
        return $this->insertSessions(array($session));
    }
    /**
     * @name 			insertSessions()
     *  				Inserts one or more sessions into database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertSessions($collection){
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
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
            else if(is_object($data) || is_array($data)){
                if(is_array($data)){
                    $obj = new \stdClass();
                    foreach($data as $key => $value){
                        $obj->$key = $value;
                    }
                    $data = $obj;
                    unset($obj);
                }
                $entity = new BundleEntity\Session;
                $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                if(!property_exists($data, 'date_created')){
                    $data->date_created = $now;
                }
                if(!property_exists($data, 'date_access')){
                    $data->date_access = $now;
                }
                if(!property_exists($data, 'site')){
                    $data->site = 1;
                }
                foreach($data as $column => $value){
                    $set = 'set'.$this->translateColumnName($column);
                    switch($column){
                        case 'member':
                            $mModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $mModel->getMember($value, 'id');
                            if(!$response['error']){
                                $entity->$set($response['result']['set']);
                            }
                            else{
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $mModel);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if(!$response['error']){
                                $entity->$set($response['result']['set']);
                            }
                            else{
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $sModel);
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
            else{
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if($countInserts > 0){
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }
    /**
     * @name 			listActions()
     *  				Listactions from database based on a variety of conditions.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listActions($filter = null, $sortorder = null, $limit = null, $query_str = null){
        $this->resetResponse();
        if(!is_array($sortorder) && !is_null($sortorder)){
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */

        /** *************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if(is_null($query_str)){
            $query_str = 'SELECT '.$this->entity['action_localization']['alias'].', '.$this->entity['action']['alias']
                .' FROM '.$this->entity['action_localization']['name'].' '.$this->entity['action_localization']['alias']
                .' JOIN '.$this->entity['action_localization']['alias'].'.action '.$this->entity['action']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if($sortorder != null){
            foreach($sortorder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'code':
                    case 'count_logs':
                    case 'date_added':
                    case 'date_added':
                    case 'date_updated':
                    case 'date_removed':
                        $column = $this->entity['action']['alias'].'.'.$column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['action_localization']['alias'].'.'.$column;
                        break;
                }
                $order_str .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY '.$order_str.' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if($filter != null){
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE '.$filter_str;
        }

        $query_str .= $where_str.$group_str.$order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if($limit != null){
            if(isset($limit['start']) && isset($limit['count'])){
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            }
            else{
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getAction()->getId();
            if(!isset($unique[$id])){
                $entities[] = $entry->getAction();
            }
        }
        $total_rows = count($entities);
        if($total_rows < 1){
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'result'     => array(
                'set'           => $entities,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			listLogs()
     *  				List logs from database based on a variety of conditions.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listLogs($filter = null, $sortorder = null, $limit = null, $query_str = null){
        $this->resetResponse();
        if(!is_array($sortorder) && !is_null($sortorder)){
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /** *************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if(is_null($query_str)){
            $query_str = 'SELECT '.$this->entity['log']['alias']
                .' FROM '.$this->entity['log']['name'].' '.$this->entity['log']['alias'];
        }

        /**
         * Prepare ORDER BY section of query.
         */
        if($sortorder != null){
            foreach($sortorder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'ip_v4':
                    case 'ip_v6':
                    case 'url':
                    case 'agent':
                    case 'date_action':
                        $column = $this->entity['log']['alias'].'.'.$column;
                        break;
                }
                $order_str .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY '.$order_str.' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if($filter != null){
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE '.$filter_str;
        }
        $query_str .= $where_str.$order_str;
        $query = $this->em->createQuery($query_str);
        /**
         * Prepare LIMIT section of query
         */
        if($limit != null){
            if(isset($limit['start']) && isset($limit['count'])){
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            }
            else{
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if($total_rows < 1){
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'result'     => array(
                'set'           => $result,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			listLoggedActionsAdded()
     *  				List logged actions added during given dates.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->lşstLogs()
     *
     * @param           mixed           $date                   One DateTime object or start and end DateTime objects.
     * @param           string          $eq                     after, before, between
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    private function listLoggedActionsAdded($date, $eq, $sortorder = null, $limit = null) {
        $this->resetResponse();
        $eq_opts = array('after', 'before', 'between', 'on');
        if (!$date instanceof \DateTime && !is_array($date)) {
            return $this->createException('InvalidParameterException', 'DateTime object or Array', 'err.invalid.parameter.date');
        }
        if (!in_array($eq, $eq_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $eq_opts), 'err.invalid.parameter.eq');
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['log']['alias'] . '.date_action';

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
        } else {
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
        $response = $this->listLogs($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }
    /**
     * @name 			listLoggedActionsAddedBetween()
     *  				List logs in between given dates.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           \DateTime       $startDate
     * @param           \DateTime       $endDate
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listLoggedActionsAddedBetween(\DateTime $startDate, \DateTime $endDate, $sortorder = null, $limit = null){
        return $this->listLoggedActionsAdded(array($startDate, $endDate), 'between', $sortorder, $limit);
    }
    /**
     * @name 			listSessions()
     *  				List sessions from database based on a variety of conditions.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listSessions($filter = null, $sortorder = null, $limit = null, $query_str = null){
        $this->resetResponse();
        if(!is_array($sortorder) && !is_null($sortorder)){
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */

        /** *************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if(is_null($query_str)){
            $query_str = 'SELECT '.$this->entity['session']['alias']
                .' FROM '.$this->entity['session']['name'].' '.$this->entity['session']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if($sortorder != null){
            foreach($sortorder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'session_id':
                    case 'username':
                    case 'date_created':
                    case 'date_login':
                    case 'date_logout':
                    case 'date_access':
                        $column = $this->entity['session']['alias'].'.'.$column;
                        break;
                }
                $order_str .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY '.$order_str.' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if($filter != null){
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE '.$filter_str;
        }

        $query_str .= $where_str.$group_str.$order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if($limit != null){
            if(isset($limit['start']) && isset($limit['count'])){
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            }
            else{
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if($total_rows < 1){
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'result'     => array(
                'set'           => $result,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			updateAction()
     *  				Updates single action. The data must be either a post data (array) or an entity
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->updateActions()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateAction($data, $by = 'post'){
        return $this->updateActions(array($data), $by);
    }
    /**
     * @name 			updateActions()
     *  				Updates one or more action details in database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     * @param           array           $by              entity, post
     *
     * @return          array           $response
     */
    public function updateActions($collection, $by = 'post'){
        $this->resetResponse();
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $by_opts = array('entity', 'post');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if($by == 'entity'){
            $sub_response = $this->update_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Action');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.delete.done',
                );
                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $to_update = array();
        $count = 0;
        $collection_by_id = array();
        foreach($collection as $item){
            if(!isset($item['id'])){
                unset($collection[$count]);
            }
            $to_update[] = $item['id'];
            $collection_by_id[$item['id']] = $item;
            $count++;
        }
        unset($collection);
        $filter = array(
            array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => array('column' => $this->entity['action']['alias'].'.id', 'comparison' => 'in', 'value' => $to_update),
                    )
                )
            )
        );
        $response = $this->listActions($filter);
        if($response['error']){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $entities = $response['result']['set'];
        foreach($entities as $entity){
            $data = $collection_by_id[$entity->getId()];
            /** Prepare foreign key data for process */
            $localizations = array();
            if(isset($data['localizations'])){
                $localizations = $data['localizations'];
            }
            unset($data['localizations']);
            $site ='';
            if(isset($data['site'])){
                $site = $data['site'];
            }
            unset($data['site']);

            foreach($data as $column => $value){
                $method_set = 'set'.$this->translateColumnName($column);
                $method_get = 'get'.$this->translateColumnName($column);
                /**
                 * Set the value only if there is a corresponding value in collection and if that value is different
                 * from the one set in database
                 */
                if(isset($collection_by_id[$entity->getId()][$column]) && $collection_by_id[$entity->getId()][$column] != $entity->$method_get()){
                    $entity->$method_set($value);
                }
                /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
                $l_collection = array();
                foreach($localizations as $lang => $localization){
                    $MLSModel = new MLSService\MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);
                    $response = $MLSModel->getLanguage($lang, 'iso_code');
                    if($response['error']){
                        new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                        break;
                    }
                    $language = $response['result']['set'];
                    $translation_exists = true;
                    $response = $this->getAction($entity, $language);
                    if($response['error']){
                        $localization_entity = new BundleEntity\Action;
                        $translation_exists = false;
                    }
                    else{
                        $localization_entity = $response['result']['set'];
                    }
                    foreach($localization as $key => $value){
                        $l_method = 'set_'.$key;
                        switch($key){
                            case 'product':
                                $localization_entity->setAction($entity);
                                break;
                            case 'language';
                                $language = $response['result']['set'];
                                $localization_entity->setLanguage($language);
                                unset($language, $response, $MLSModel);
                                break;
                            default:
                                $localization_entity->$l_method($value);
                                break;
                        }
                    }
                    $l_collection[] = $localization_entity;
                    if(!$translation_exists){
                        $this->em->persist($localization_entity);
                    }
                }
                $entity->setLocalizations($l_collection);
                /** HANDLE FOREIGN DATA :: SITE */
                if(is_numeric($site)){
                    $SMModel = new SMMService\SiteManagementModel($this->kernel, $this->db_connection, $this->orm);
                    $response = $SMModel->getSite($site, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $site_entity = $response['result']['set'];
                    $entity->$method_set($site_entity);
                    /** Free up some memory */
                    unset($site, $response, $SMModel);
                }
                $this->em->persist($entity);
            }
        }
        $this->em->flush();

        $total_rows = count($to_update);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $to_update,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.update.done',
        );
        return $this->response;
    }
    /**
     * @name 			updateLog()
     *  				Updates single log. The data must be either a post data (array) or an entity
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->updateLogs()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateLog($data, $by = 'post'){
        return $this->updateLogs(array($data), $by);
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
     * @param           array           $collection      Collection of Product entities or array of entity details.
     * @param           array           $by              entity, post
     *
     * @return          array           $response
     */
    public function updateLogs($collection, $by = 'post'){
        $this->resetResponse();
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $by_opts = array('entity', 'post');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if($by == 'entity'){
            $sub_response = $this->update_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Log');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.delete.done',
                );
                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $to_update = array();
        $count = 0;
        $collection_by_id = array();
        foreach($collection as $item){
            if(!isset($item['id'])){
                unset($collection[$count]);
            }
            $to_update[] = $item['id'];
            $collection_by_id[$item['id']] = $item;
            $count++;
        }
        unset($collection);
        $filter = array(
            array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => array('column' => $this->entity['action']['alias'].'.id', 'comparison' => 'in', 'value' => $to_update),
                    )
                )
            )
        );
        $response = $this->listLogs($filter);
        if($response['error']){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $entities = $response['result']['set'];
        foreach($entities as $entity){
            $data = $collection_by_id[$entity->getId()];
            /** Prepare foreign key data for process */
            $site ='';
            if(isset($data['site'])){
                $site = $data['site'];
            }
            unset($data['site']);
            $session ='';
            if(isset($data['session'])){
                $session = $data['session'];
            }
            unset($data['session']);
            $action ='';
            if(isset($data['action'])){
                $action = $data['action'];
            }
            unset($data['site']);
            foreach($data as $column => $value){
                $method_set = 'set'.$this->translateColumnName($column);
                $method_get = 'get'.$this->translateColumnName($column);
                /**
                 * Set the value only if there is a corresponding value in collection and if that value is different
                 * from the one set in database
                 */
                if(isset($collection_by_id[$entity->getId()][$column]) && $collection_by_id[$entity->getId()][$column] != $entity->$method_get()){
                    $entity->$method_set($value);
                }
                /** HANDLE FOREIGN DATA :: SITE */
                if(is_numeric($site)){
                    $SMModel = new SMMService\SiteManagementModel($this->kernel, $this->db_connection, $this->orm);
                    $response = $SMModel->getSite($site, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $site_entity = $response['result']['set'];
                    $entity->$method_set($site_entity);
                    /** Free up some memory */
                    unset($site, $response, $SMModel);
                }
                /** HANDLE FOREIGN DATA :: ACTION */
                if(is_numeric($action)){
                    $response = $this->getAction($action, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $action_entity = $response['result']['set'];
                    $entity->$method_set($action_entity);
                    /** Free up some memory */
                    unset($action, $response);
                }
                /** HANDLE FOREIGN DATA :: SESSION */
                if(is_numeric($session)){
                    $response = $this->getSession($session, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $session_entity = $response['result']['set'];
                    $entity->$method_set($session_entity);
                    /** Free up some memory */
                    unset($site, $response);
                }
                $this->em->persist($entity);
            }
        }
        $this->em->flush();

        $total_rows = count($to_update);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $to_update,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.update.done',
        );
        return $this->response;
    }
    /**
     * @name 			updateSession()
     *  				Updates single log. The data must be either a post data (array) or an entity
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->updateSessions()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateSession($data, $by = 'post'){
        return $this->updateSessions(array($data), $by);
    }
    /**
     * @name 			updateSessions()
     *  				Updates one or more log details in database.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     * @param           array           $by              entity, post
     *
     * @return          array           $response
     */
    public function updateSessions($collection, $by = 'post'){
        $this->resetResponse();
        /** Parameter must be an array */
        if(!is_array($collection)){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $by_opts = array('entity', 'post');
        if(!in_array($by, $by_opts)){
            return $this->createException('InvalidParameterException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if($by == 'entity'){
            $sub_response = $this->update_entities($collection, '\\BiberLtd\\Core\\Bundles\\LogBundle\\Entity\\Session');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if($sub_response['process'] == 'stop'){
                $this->response = array(
                    'result'     => array(
                        'set'           => $sub_response['entries']['valid'],
                        'total_rows'    => $sub_response['item_count'],
                        'last_insert_id'=> null,
                    ),
                    'error'      => false,
                    'code'       => 'scc.db.delete.done',
                );
                return $this->response;
            }
            else{
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $to_update = array();
        $count = 0;
        $collection_by_id = array();
        foreach($collection as $item){
            if(!isset($item['id'])){
                unset($collection[$count]);
            }
            $to_update[] = $item['id'];
            $collection_by_id[$item['id']] = $item;
            $count++;
        }
        unset($collection);
        $filter = array(
            array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => array('column' => $this->entity['session']['alias'].'.id', 'comparison' => 'in', 'value' => $to_update),
                    )
                )
            )
        );
        $response = $this->listSessions($filter);
        if($response['error']){
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $entities = $response['result']['set'];
        foreach($entities as $entity){
            $data = $collection_by_id[$entity->getId()];
            /** Prepare foreign key data for process */
            $site ='';
            if(isset($data['site'])){
                $site = $data['site'];
            }
            unset($data['site']);
            $member ='';
            if(isset($data['member'])){
                $member = $data['member'];
            }
            unset($data['member']);

            foreach($data as $column => $value){
                $method_set = 'set'.$this->translateColumnName($column);
                $method_get = 'get'.$this->translateColumnName($column);
                /**
                 * Set the value only if there is a corresponding value in collection and if that value is different
                 * from the one set in database
                 */
                if(isset($collection_by_id[$entity->getId()][$column]) && $collection_by_id[$entity->getId()][$column] != $entity->$method_get()){
                    $entity->$method_set($value);
                }
                /** HANDLE FOREIGN DATA :: SITE */
                if(is_numeric($site)){
                    $SMModel = new SMMService\SiteManagementModel($this->kernel, $this->db_connection, $this->orm);
                    $response = $SMModel->getSite($site, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $site_entity = $response['result']['set'];
                    $entity->$method_set($site_entity);
                    /** Free up some memory */
                    unset($site, $response, $SMModel);
                }
                /** HANDLE FOREIGN DATA :: Member */
                if(is_numeric($member)){
                    $response = $this->getMember($member, 'id');
                    if($response['error']){
                        new CoreExceptions\InvalidSiteException($this->kernel, $value);
                        break;
                    }
                    $action_entity = $response['result']['set'];
                    $entity->$method_set($action_entity);
                }
                $this->em->persist($entity);
            }
        }
        $this->em->flush();

        $total_rows = count($to_update);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'result'     => array(
                'set'           => $to_update,
                'total_rows'    => $total_rows,
                'last_insert_id'=> null,
            ),
            'error'      => false,
            'code'       => 'scc.db.update.done',
        );
        return $this->response;
    }
}

/**
 * Change Log
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

