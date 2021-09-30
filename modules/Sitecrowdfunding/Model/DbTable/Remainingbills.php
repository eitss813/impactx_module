<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Remainingbills.php 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Remainingbills extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_remaining_bills';

    /**
     * Return project_id
     *
     * Is project remaining bill exist or not
     * @param $project_id
     * @return object
     */
    public function isProjectRemainingBillExist($project_id) {
        $select = $this->select()
                        ->from($this->info('name'), array("project_id"))
                        ->where('project_id =?', $project_id)
                        ->query()->fetchColumn();

        return $select;
    }

}
