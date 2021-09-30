<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_SpecificationSitealbumController extends Seaocore_Content_Widget_Abstract {

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

    //GET QUICK INFO DETAILS
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitealbum/View/Helper', 'Sitealbum_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitealbum);
    $this->view->fieldValue = $this->view->FieldValueLoopQuickInfoSitealbum($sitealbum, $this->view->fieldStructure);

    if (empty($this->view->fieldValue)) {
      return $this->setNoRender();
    }
  }

}