<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Adminnotes extends Engine_Db_Table
{

    protected $_rowClass = 'Sitepage_Model_Adminnote';


    public function getAllAdminNotesByProjectId($page_id){
        //MAKE QUERY
        $select = $this->select()
            ->where('page_id = ?', $page_id);
        //RETURN RESULTS
        $result =  $select->query()->fetchAll();
        return $result;
    }
}