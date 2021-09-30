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
class Sitepage_Widget_ZeropageSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create');
    $values['type'] = 'browse_home_zero';
    $this->view->assign($values);
    $values['limit'] = 1;

    // GET SITEPAGES
    $sitepage = Engine_Api::_()->sitepage()->getSitepagesPaginator($values);

    if ((count($sitepage) > 0)) {
      return $this->setNoRender();
    }
  }

}

?>
