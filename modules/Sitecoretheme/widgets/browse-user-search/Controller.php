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
class Sitecoretheme_Widget_BrowseUserSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Prepare form
    $this->view->form = $form = new Sitecoretheme_Form_User_Search(array(
      'type' => 'user'
    ));

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->populate($p);
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
  }

}