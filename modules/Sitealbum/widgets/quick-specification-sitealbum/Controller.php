<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_QuickSpecificationSitealbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1))
      return $this->setNoRender();
    
    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('album')) {
      return $this->setNoRender();
    } else {
      $this->view->sitealbum = $sitealbum = Engine_Api::_()->core()->getSubject();
    }

    //LISITNG SHOULD BE MAPPED WITH PROFILE
    if (empty($this->view->sitealbum->profile_type)) {
      return $this->setNoRender();
    }

    $itemCount = $this->_getParam('itemCount', 5);

    //GET QUICK INFO DETAILS
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitealbum/View/Helper', 'Sitealbum_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitealbum);

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSitealbum($sitealbum, $this->view->fieldStructure, $itemCount);
    }

    if (empty($this->view->show_fields)) {
      return $this->setNoRender();
    }
  }

}