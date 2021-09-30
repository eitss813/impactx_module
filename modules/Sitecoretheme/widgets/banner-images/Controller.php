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
class Sitecoretheme_Widget_BannerImagesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->getElement()->removeDecorator('Title');
    $this->view->defaultDuration = $this->_getParam("speed", 5000);
    $this->view->slideWidth = $this->_getParam("width", null);
    $this->view->slideHeight = $this->_getParam("height", 300);
    $this->view->showBanners = $this->_getParam('showBanners', 1);
    $selectedBanners = array();
    if (!$this->view->showBanners) {
      $selectedBanners = $this->_getParam('selectedBanners');

      if (empty($selectedBanners)) {
        return $this->setNoRender();
      }

      $this->view->list = $getBanners = Engine_Api::_()->getItemTable('sitecoretheme_banner')->getBanners(array('enabled' => 1, 'selectedBanners' => $selectedBanners), array('file_id'));
    } else {
      $this->view->list = $getBanners = Engine_Api::_()->getItemTable('sitecoretheme_banner')->getBanners(array('enabled' => 1), array('file_id'));
    }
    $order = $this->_getParam("order", 2);
    if (!COUNT($getBanners)) {
      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();
      $action = $front->getRequest()->getActionName();
      $controller = $front->getRequest()->getControllerName();
      switch (true) {
        case $module == "core" && $controller == 'help' && $action == 'terms':
          $this->view->list = array("terms_banner.png");
          break;

        case $module == "core" && $controller == 'help' && $action == 'contact':
          $this->view->list = array("contact_banner.png");
          break;

        case $module == "core" && $controller == 'help' && $action == 'privacy':
          $this->view->list = array("privacy_banner.jpg");
          break;
        default :
          $this->view->list = Engine_Api::_()->sitecoretheme()->setImageOrder(array("banner.png", "banner2.png", "banner3.png"), $order);
      }
    } else {
      $getBannersArray = $getBanners->toArray();
      $this->view->list = Engine_Api::_()->sitecoretheme()->setImageOrder($getBannersArray, $order);
    }

    $this->view->verticalHtmlTitle = $this->_getParam("verticalHtmlTitle", "Videos that you'd love");
    $this->view->verticalHtmlDescription = $this->_getParam("verticalHtmlDescription", "The foremost source to explore and watch videos.");
  }

}