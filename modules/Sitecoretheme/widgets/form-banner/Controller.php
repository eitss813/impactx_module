<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_FormBannerController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->logo = $this->_getParam('logo', '');
    // $this->view->description = $this->_getParam('description', '');
    $this->view->imgPath = $this->_getParam('image', '');
    $this->view->gradientColor1 = $this->_getParam('gradient_color_first', '');
    $this->view->gradientColor2 = $this->_getParam('gradient_color_second', '');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->pageIdentity = join('-', array(
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));
    $this->view->return_url = $request->getParam('return_url');
  }

}