<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Locations extends Engine_Db_Table {

    protected $_rowClass = "Sitecrowdfunding_Model_Location";

    /**
     * Get location
     *
     * @param array $params
     * @return object
     */
    public function getLocation($params = array()) {


        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {

            $locationName = $this->info('name');

            $select = $this->select();
            if (isset($params['id'])) {
                $select->where('project_id = ?', $params['id']);
                return $this->fetchRow($select);
            }

            if (isset($params['project_ids'])) {
                $select->where('project_id IN (?)', (array) $params['project_ids']);
                return $this->fetchAll($select);
            }
        }
    }
    
    public function getLocationRow($project_id){
        $select = $this->select();
        $select->where('project_id = ?', $project_id);
        return $this->fetchRow($select);
    }

    public function getProjectsLocation($params = array()) {

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
        $project_ids = $pagesTable->getPageProjects($params['page_id']);

        if (count($project_ids) > 0) {

            $locationName = $this->info('name');
            $select = $this->select();
            $select->where('project_id IN (?)', $project_ids);

            return $this->fetchAll($select);
        }else{
            return null;
        }
    }


}