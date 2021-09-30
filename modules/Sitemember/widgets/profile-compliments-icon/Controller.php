<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ProfileComplimentsIconController extends Engine_Content_Widget_Abstract {

    public function indexAction() {  
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $iconCount = $this->_getParam('iconCount',10);
        $this->view->viewType = $this->_getParam('viewType',0);
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $params = array("resource_type" => $subject->getType(),"resource_id"=> $subject->getIdentity(),"limit"=>$iconCount,"groupby"=>"complimentcategory_id");
        $this->view->complimentTable = $complimentTable = Engine_Api::_()->getDbTable("compliments","sitemember");
        
        $complimentsSelect = $complimentTable->getCompliments($params); 
        $this->view->compliments = $compliments = $complimentTable->fetchAll($complimentsSelect);
        $this->view->complimentsCount = $complimentTable->getComplimentCount(array('resource_id' => $subject->getIdentity()));
        if(count($compliments)<1){
            return $this->setNoRender();
        }
    }

}
