<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_SitemobileOverviewSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

   
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
    	$this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    }
    else {
      $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //START MANAGE-ADMIN CHECK   
    $this->view->can_edit_overview = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $this->view->can_edit = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if (empty($this->view->can_edit) && empty($sitepage->overview)) {
      return $this->setNoRender();
    } 

    //GET SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

  }
}

?>