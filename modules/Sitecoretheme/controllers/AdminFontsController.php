<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFontsController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminFontsController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_theme_custom');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_theme_custom', array(), 'sitecoretheme_admin_font_index');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $font = $coreSettings->getSetting('sitecoretheme.fonts.selected.font', 0);
    $fontType = $this->getRequest()->getParam('fontType', $font);
    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Fonts(array('fontType' => $fontType));

    if( !$this->getRequest()->isPost() )
      return;

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    $fileName = 'sitecorethemeThemeGeneralConstants.css';
    $valuesWithPx = $values;
    $valuesWithPx['sitecoretheme_fonts_body_font_size'] = $valuesWithPx['sitecoretheme_fonts_body_font_size'].'px';
    $valuesWithPx['sitecoretheme_fonts_heading_fontsize'] = $valuesWithPx['sitecoretheme_fonts_heading_fontsize'].'px';
    $valuesWithPx['sitecoretheme_fonts_mainmenu_font_size'] = $valuesWithPx['sitecoretheme_fonts_mainmenu_font_size'].'px';
    $valuesWithPx['sitecoretheme_fonts_tab_font_size'] = $valuesWithPx['sitecoretheme_fonts_tab_font_size'].'px';
 
    if($fontType == 0) {
      //WE HAVE TO SAVE THE GOOGLE FONT AND WEB SAFE IN ONE CONSTANT
      $valuesWithPx['sitecoretheme_fonts_body_font_family'] = $valuesWithPx['sitecoretheme_fonts_body_font_family_google'];
      $valuesWithPx['sitecoretheme_fonts_heading_font_family'] = $valuesWithPx['sitecoretheme_fonts_heading_font_family_google'];
      $valuesWithPx['sitecoretheme_fonts_mainmenu_font_family'] = $valuesWithPx['sitecoretheme_fonts_mainmenu_font_family_google'];
      $valuesWithPx['sitecoretheme_fonts_tab_font_family'] = $valuesWithPx['sitecoretheme_fonts_tab_font_family_google'];
      unset($valuesWithPx['sitecoretheme_fonts_body_font_family_google'], $valuesWithPx['sitecoretheme_fonts_heading_font_family_google'], $valuesWithPx['sitecoretheme_fonts_mainmenu_font_family_google'], $valuesWithPx['sitecoretheme_fonts_tab_font_family_google']);
    }

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