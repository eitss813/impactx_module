<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLayoutController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminLayoutController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_theme_custom');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_theme_custom', array(), 'sitecoretheme_admin_layout_index');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Layout();


    if( !$this->getRequest()->isPost() )
      return;

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    $values = $form->getValues();

    $fileName = 'sitecorethemeThemeGeneralConstants.css';
    $valuesWithPx = $values;
    $valuesWithPx['sitecoretheme_layout_theme_width'] = $valuesWithPx['sitecoretheme_layout_theme_width'].'px';
    $valuesWithPx['sitecoretheme_layout_left_column_width'] = $valuesWithPx['sitecoretheme_layout_left_column_width'].'px';
    $valuesWithPx['sitecoretheme_layout_right_column_width'] = $valuesWithPx['sitecoretheme_layout_right_column_width'].'px';
//    $valuesWithPx['sitecoretheme_landing_heading_icon'] = $valuesWithPx['sitecoretheme_landing_heading_icon'];
//    $valuesWithPx['sitecoretheme_layout_container_headding_style'] = $valuesWithPx['sitecoretheme_layout_container_headding_style'];
    $successfullySaved = Engine_Api::_()->getApi('customization', 'sitecoretheme')->setConstants($valuesWithPx, $fileName);

    foreach( $values as $key => $value ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    }

    if( !empty($successfullySaved) ) {
      Core_Model_DbTable_Themes::clearScaffoldCache();

      $form->addNotice('Changes successfully saved. If changes not reflect to frontend then please change the mode to Development.');
    }
  }

}