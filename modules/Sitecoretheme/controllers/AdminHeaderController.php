<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminHeaderController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminHeaderController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_header_index');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Header();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    foreach( $values as $key => $value ) {
      if( $coreSettings->hasSetting($key, $value) ) {
        $coreSettings->removeSetting($key);
      }
      if( in_array($key, array('sitecoretheme_header_loggedout_widgets', 'sitecoretheme_header_loggedin_widgets')) ) {
        $coreSettings->setSetting($key, array('none'));
      }
      if( $value == null ) {
        continue;
      }
      $coreSettings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }

}