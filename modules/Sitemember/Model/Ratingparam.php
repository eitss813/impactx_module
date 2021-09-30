<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratingparam.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_Ratingparam extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    /**
     * Delete the belongings
     * 
     */
    public function _delete() {

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            Engine_Api::_()->getDbTable('ratings', 'sitemember')->delete(array('ratingparam_id = ?' => $this->ratingparam_id));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        parent::_delete();
    }

}