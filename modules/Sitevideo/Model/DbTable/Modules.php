<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modules.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Modules extends Engine_Db_Table {

    protected $_name = 'sitevideo_modules';
    protected $_rowClass = 'Sitevideo_Model_Module';

    public function getModuleTitle($name) {
        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        $coreTableName = $coreTable->info('name');
        $select = $coreTable->select()
                ->from($coreTableName, array('title'))
                ->where('name =?', $name);
        $title = $select->query()->fetchColumn();
        return $title;
    }

    public function getIntegratedModules($params = array()) {

        $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $moduleTableName = $this->info('name');
        $coreModuleTableName = $coreModuleTable->info('name');
        $select = $this->select()->setIntegrityCheck(false)
                ->from($moduleTableName, array('*'))
                ->join($coreModuleTableName, $coreModuleTableName . '.name = ' . $moduleTableName . '.item_module', array())
                ->where($coreModuleTableName . '.enabled =?', 1);


        if (isset($params['enabled']) && !empty($params['enabled'])) {
            $select->where($moduleTableName . '.enabled =?', 1);
        }

        if (isset($params['item_module']) && !empty($params['item_module'])) {
            $select->where($moduleTableName . '.item_module =?', $params['item_module']);
        }

        if (isset($params['item_type']) && !empty($params['item_type'])) {
            $select->where($moduleTableName . '.item_type =?', $params['item_type']);
        }

        $results = $this->fetchAll($select)->toArray();

        if (empty($results) && isset($params['checked']) && !empty($params['checked'])) {
            return false;
        }
        if (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo'))) {
            return true;
        } elseif (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessvideo'))) {
            return true;
        } elseif (empty($results) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo'))) {
            return true;
        }

        if (empty($results)) {
            return false;
        }

        if (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) && (isset($params['item_module']) && $params['item_module'] == 'sitepage')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessvideo')) && (isset($params['item_module']) && $params['item_module'] == 'sitebusiness')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) && (isset($params['item_module']) && $params['item_module'] == 'sitegroup')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) && (isset($params['item_module']) && $params['item_module'] == 'sitestore')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')) && (isset($params['item_module']) && $params['item_module'] == 'sitereview')) {
            return true;
        } elseif (($results || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) && (isset($params['item_module']) && $params['item_module'] == 'siteevent')) {
            return true;
        }
        return $results;
    }

    public function getIntegratedItemTitle($item_module, $item_type) {

        $title = $this->select()
                        ->from($this->info('name'), 'item_title')
                        ->where('item_module =?', $item_module)
                        ->where('item_type =?', $item_type)
                        ->query()->fetchColumn();

        if (empty($title))
            $title = $this->getModuleTitle($item_module);
        return $title;
    }

}
