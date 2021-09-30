<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectGateways.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Initiativequestions extends Engine_Db_Table {

    protected $_rowClass = 'Sitepage_Model_Initiativequestion';

    public function getAllInitiativesQuestionsById($page_id,$initiativeId){

        $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('initiative_id = ?', $initiativeId)
            ->order('creation_date DESC')
            ->order('updated_date DESC');

        $result =  $select->query()->fetchAll();

        return $result;
    }

    public function getAllInitiativesQuestionsByInitiativeId($initiativeId){

        $select = $this->select()
            ->where('initiative_id = ?', $initiativeId)
            ->order('creation_date DESC')
            ->order('updated_date DESC');

        $result =  $select->query()->fetchAll();

        return $result;
    }


}
