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
class Sitecoretheme_Widget_HeadingController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Get block
    $this->view->title = $this->_getParam('title', '');
    $this->view->description = $this->_getParam('description', '');
    if (!$this->view->title) {
      return $this->setNoRender();
    }
  }

}