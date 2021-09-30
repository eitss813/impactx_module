<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Widget_LoginOrSignupPopupController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->pageIdentity = join('-', array(
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));
    $notRenderPages = array('user-signup-index', 'user-auth-login'); 
    if( Engine_Api::_()->user()->getViewer()->getIdentity() || in_array($this->view->pageIdentity, $notRenderPages) ) {
      $this->setNoRender();
      return;
    }
  }

  public function getCacheKey()
  {
    return false;
  }
}
