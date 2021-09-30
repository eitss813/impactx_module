<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Api_Core extends Core_Api_Abstract {

  public function getFileUrl($image) {
    
    $table = Engine_Api::_()->getDbTable('files', 'core');
    $result = $table->select()
                ->from($table->info('name'), 'storage_file_id')
                ->where('storage_path =?', $image)
                ->query()
                ->fetchColumn();
    if(!empty($result)) {
      $storage = Engine_Api::_()->getItem('storage_file', $result);
      return $storage->map();
    } else {
      return $image;
    }
  }
  
    public function networksJoinedMembers($networks) {
        $userNetworkTable = Engine_Api::_()->getDbtable('membership', 'network');
        $userNetworkTableName = $userNetworkTable->info('name');

        $selectN = $userNetworkTable->select()->where('resource_id IN (?)', $networks);
        $resultsN = $userNetworkTable->fetchAll($selectN);
        return $resultsN;
    }

  	public function profileTypesMembers($profileTypes) {

        $valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
        $valuesTableName = $valuesTable->info('name');
        $select = $valuesTable->select()
                                ->from($valuesTableName)
                                ->where($valuesTableName . '.value IN (?)', $profileTypes)
                                ->where($valuesTableName . '.field_id = ?', 1);
        $results = $valuesTable->fetchAll($select);
        return $results;
    }

  	public function todayBirthdayMembers() {

        $userTable = Engine_Api::_()->getDbTable('users', 'user');
        $userTableName = $userTable->info('name');
        $metaTableName = 'engine4_user_fields_meta';
        $valueTableName = 'engine4_user_fields_values';
        $select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($userTableName, array('email', 'user_id'))
                ->join($valueTableName, $valueTableName . '.item_id = ' . $userTableName . '.user_id', null)
                ->join($metaTableName, $metaTableName . '.field_id = ' . $valueTableName . '.field_id', array())
                ->where($metaTableName . '.type = ?', 'birthdate')
                ->where("DATE_FORMAT(" . $valueTableName . " .value, '%m-%d') = ?", date('m-d'));
        $results = $userTable->fetchAll($select);
        return $results;
    }

    public function getModulesEnable() {

        $modules = Engine_Api::_()->getDbTable('modules','core')->getEnabledModuleNames();
        $moduleArray = array();

        $getResults = Engine_Api::_()->getDbTable('integrateothersmodules','sesnewsletter')->getResults(array('enabled' => 1));

        foreach($getResults as $getResult) {
            $getModules = $this->getModules($getResult['module_name']);
            if(!empty($getModules)) {
                $moduleArray[$getResult['content_type']] = $getModules;
            }
        }
        return $moduleArray;
    }

    public function getPluginItem($moduleName) {
            //initialize module item array
        $moduleType = array();
        $filePath =  APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
            //check file exists or not
        if (is_file($filePath)) {
                //now include the file
        $manafestFile = include $filePath;
                $resultsArray =  Engine_Api::_()->getDbtable('integrateothermodules', 'sesbasic')->getResults(array('module_name'=>$moduleName));
        if (is_array($manafestFile) && isset($manafestFile['items'])) {
            foreach ($manafestFile['items'] as $item)
            if (!in_array($item, $resultsArray))
                $moduleType[$item] = $item.' ';
        }
        }
        return $moduleType;
    }

    public function addSubscriber($user_id, $type_id) {

        $user = Engine_Api::_()->getItem('user', $user_id);
        $table = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter');

        $isExist = $table->isExistType($user->email, $type_id);
        if(empty($isExist)) {

            $values = array('resource_id' => $user_id, 'level_id' => $user->level_id, 'email' => $user->email, 'resource_type' => 'user', 'displayname' => $user->getTitle(), 'type_id' => $type_id);

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $item = $table->createRow();
                $item->setFromArray($values);
                $item->save();
                $db->commit();
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'sesnewsletter_mobile_subscribe', array('sender_title' => $user->getTitle(), 'host' => $_SERVER['HTTP_HOST']));
            } catch(Exception $e) {
                $db->rollBack();
                //throw $e;
            }
        }
    }

    public function getModules($module_name) {

        $table = Engine_Api::_()->getDbTable('modules','core');
        $tableName = $table->info('name');
        return $table->select()
                        ->from($table, array('title'))
                        ->where('name =?', $module_name)
                        ->where('enabled =?', 1)
                        ->query()
                        ->fetchColumn();
    }

    public function getUserId($email) {

        $table = Engine_Api::_()->getDbTable('users','user');
        return Engine_Api::_()->getDbTable('users','user')->select()
                        ->from($table, array('user_id'))
                        ->where('email =?', $email)
                        ->query()
                        ->fetchColumn();
    }
}
