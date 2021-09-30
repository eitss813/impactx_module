<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_ProjectContactDetailsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        } 
        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $this->view->address = $address = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_address');
        $this->view->phone = $phone = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_phone');
        $this->view->email = $email = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_email');

        if (!$address && !$email && !$phone){
            return $this->setNoRender();
        }
    }
}
