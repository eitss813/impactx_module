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
class Sitemember_Widget_ComplimentMeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() == 'user' && $subject->getIdentity() == $viewer->getIdentity() ) {
      return $this->setNoRender();
    }

    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    if( !Engine_Api::_()->authorization()->isAllowed('user', $viewer, 'compliment') ) {
      return $this->setNoRender();
    }


    $this->view->compliment_button_title = $this->_getParam('compliment_button_title', 'Compliment Me !');
  }

}
