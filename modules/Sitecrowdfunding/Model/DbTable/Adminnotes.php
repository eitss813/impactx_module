<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Adminnotes extends Engine_Db_Table
{

    protected $_rowClass = 'Sitecrowdfunding_Model_Adminnote';


    public function getAllAdminNotesByProjectId($project_id, $is_funding){
        //MAKE QUERY
        $select = $this->select()
            ->where('project_id = ?', $project_id)
            ->where('is_funding = ?',$is_funding);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return $result;
    }
}