<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminSettingsController extends Core_Controller_Action_Admin
{

  public function __call($method, $params)
  {
    /*
     * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
     * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
     * REMEMBER:
     *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
     *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
     */

    if( !empty($method) && $method == 'Sitecoretheme_Form_Admin_Settings' ) {
      
    }
    return true;
  }





  public function indexAction()
  {  
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_index');

    $this->view->isModsSupport = Engine_Api::_()->sitecoretheme()->isModulesSupport();
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Global();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
      
      foreach( $values as $key => $value ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      } 
      $form->addNotice('Your changes have been saved.'); 
    }
  }

  public function configurePagesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_configure_pages');

     
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Configure();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
      
      foreach( $values as $key => $value ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $URL = $view->baseUrl() . '/admin/content';

      $settingsTable = Engine_Api::_()->getDbtable('settings', 'core'); 
      $row = $settingsTable->fetchRow($settingsTable->select()
          ->where('name = ?', 'sitecoretheme.template.name'));
      $template = $row ? $row->value : null;    
      $module = 'sitecoretheme';
      if($template) {
        $module = $template;
      }

      if ($values['sitecoretheme_landing_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', $module)->setDefaultLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'landing_page_backup', 'name' => 'core_index_index'));
      }

      if ($values['sitecoretheme_header_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setHeaderLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'header_backup', 'name' => 'header'));
      }

      if ($values['sitecoretheme_footer_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setFooterLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'footer_backup', 'name' => 'footer'));
      }

      if ($values['sitecoretheme_login_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInPageLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'user_auth_login_backup', 'name' => 'user_auth_login'));
      }

      if ($values['sitecoretheme_login_required_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInRequiredPageLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'core_error_requireuser_backup', 'name' => 'core_error_requireuser'));
      }
      
      if ($values['sitecoretheme_signup_page_layout']) {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignUpPageLayout($values);
      } else {
        Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->restorePageBackup(array('pageUrl' => 'user_signup_index_backup', 'name' => 'user_signup_index'));
      }
      $form->addNotice('Your changes have been saved. Please check your pages layout from <a href="' . $URL . '" target="_blank">here</a>.');
    }
  }

  public function activateAction() {
    //CHANGE LAYOUT OF HEADER,FOOTER,SIGNIN,SIGNUP AND LANDING PAGE
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setDefaultLayout(array('sitecoretheme_landing_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setHeaderLayout(array('sitecoretheme_header_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setFooterLayout(array('sitecoretheme_footer_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInPageLayout(array('sitecoretheme_login_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInRequiredPageLayout(array('sitecoretheme_login_required_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignUpPageLayout(array('sitecoretheme_signup_page_layout' => 1));

    $redirect = $this->_getParam('redirect', false);
    if( $redirect == 'install' ) {
      $this->_redirect('install/manage');
    } elseif( $redirect == 'query' ) {
      $this->_redirect('install/manage/complete');
    }
  }


  public function signinPopupAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_signin_popup');

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_SigninPopup();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();

      foreach( $values as $key => $value ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function landingImagesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_img');

     $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_settings_img', array(), 'sitecoretheme_admin_settings_landing_images');
    $this->view->list = Engine_Api::_()->getItemTable('sitecoretheme_image')->getImages();
  }

  public function innerImagesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_img');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_settings_img', array(), 'sitecoretheme_admin_settings_inner_images');
    $this->view->list = Engine_Api::_()->getItemTable('sitecoretheme_banner')->getBanners();
  }

  public function setOrderAction()
  {
    if( empty($_POST) || empty($_POST['order']) ) {
      return;
    }

    $item = $_POST['item'];
    foreach( $_POST['order'] as $key => $value ) {
      if( strstr($key, "content_") ) {
        $keyArray = explode("content_", $key);
        $itemId = end($keyArray);

        if( !empty($itemId) ) {
          $obj = Engine_Api::_()->getItem($item, $itemId);
          print_r($obj->toArray());
          $obj->order = $value;
          $obj->save();
        }
      }
    }
  }

  public function addImagesAction()
  {
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Images_Add();
    $table = Engine_Api::_()->getItemTable('sitecoretheme_image');
    //CHECK POST
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    //CHECK VALIDITY
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    $db = $table->getAdapter();
    $db->beginTransaction();
    try {

      $title = Engine_Api::_()->getItemTable('sitecoretheme_image')->getTitleMatch($values['title']);

      $row = $table->createRow();
      $row->setFromArray($values);
      $id = $row->save();

      if( !empty($values['photo']) ) {
        $row->setPhoto($form->photo);
      }


      if( $title ) {
        $values['title'] = $values['title'] . '-' . $id;
      }

      if( $row ) {
        $row->title = $values['title'];
        $row->save();
      }

      //COMMIT
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Image Successfully Added'))
      ));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function addBannersAction()
  {
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Banners_Add();
    $table = Engine_Api::_()->getItemTable('sitecoretheme_banner');
    //CHECK POST
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    //CHECK VALIDITY
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $title = Engine_Api::_()->getItemTable('sitecoretheme_banner')->getTitleMatch($values['title']);

      $row = $table->createRow();
      $row->setFromArray($values);
      $id = $row->save();

      if( !empty($values['photo']) ) {
        $row->setPhoto($form->photo);
      }

      if( $title ) {
        $values['title'] = $values['title'] . '-' . $id;
      }

      if( $row ) {
        $row->title = $values['title'];
        $row->save();
      }

      //COMMIT
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Image Successfully Added'))
      ));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteAction()
  {

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->id = $id = $this->_getParam('id');

    if( $this->getRequest()->isPost() ) {
      $item = Engine_Api::_()->getItem('sitecoretheme_image', $id);

      $item->delete();
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('Deleted Succesfully.')
      ));
    }
  }

  public function enabledAction()
  {
    $id = $this->_getParam('id');
    if( !empty($id) ) {
      $item = Engine_Api::_()->getItem('sitecoretheme_image', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }

    $this->_redirect('admin/sitecoretheme/settings/landing-images');
  }

  public function enabledBannersAction()
  {
    $id = $this->_getParam('id');
    if( !empty($id) ) {
      $item = Engine_Api::_()->getItem('sitecoretheme_banner', $id);
      $item->enabled = !$item->enabled;
      $item->save();
    }

    $this->_redirect('admin/sitecoretheme/settings/inner-images');
  }

  public function deleteBannersAction()
  {

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->id = $id = $this->_getParam('id');

    if( $this->getRequest()->isPost() ) {
      $item = Engine_Api::_()->getItem('sitecoretheme_banner', $id);

      $item->delete();
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('Deleted Succesfully.')
      ));
    }
  }

  public function faqAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_settings_faq');
    $this->view->faq_id = $faq_id = $this->_getParam('faq', 'faq_1');
  }

  public function footerMenuAction()
  {
    $this->_redirect('admin/menus/index?name=sitecoretheme_footer');
  }

  public function placeHtaccessFileAction()
  {
    if( $this->getRequest()->isPost() ) {
      $successfullyAdded = false;
      $getFileContent = '<FilesMatch ".(ttf|otf|woff)$">
    Header set Access-Control-Allow-Origin "*"
</FilesMatch>';

      $global_directory_name = APPLICATION_PATH . '/application/themes/sitecoretheme';
      $global_settings_file = $global_directory_name . '/.htaccess';
      $is_file_exist = @file_exists($global_settings_file);

      // IF FILE NOT EXIST THEN CREATE NEW .HTACCESS FILE THERE.
      if( empty($is_file_exist) ) {
        if( is_dir($global_directory_name) ) {
          @mkdir($global_directory_name, 0777);

          $fh = @fopen($global_settings_file, 'w') or die('Unable to create .htaccess file; please give the CHMOD 777 recursive permission to the directory "' . APPLICATION_PATH . '/application/themes/sitecoretheme' . '" and then try again.');
          @fwrite($fh, $getFileContent);
          @fclose($fh);

          @chmod($global_settings_file, 0777);
          $successfullyAdded = true;
        }
      } else {
        if( !is_writable($global_settings_file) ) {
          @chmod($global_settings_file, 0777);
          if( !is_writable($global_settings_file) ) {
            $form->addError('Unable to create .htaccess file; please give the CHMOD 777 recursive permission to the directory "' . APPLICATION_PATH . '/application/themes/sitecoretheme' . '" and then try again.');
            return;
          }
        }
        $successfullyAdded = @file_put_contents($global_settings_file, $getFileContent);
      }

      if( !empty($successfullyAdded) ) {
        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('File Succesfully Created.')
        ));
      }
    }
  }

  public function placeCustomizationFileAction()
  {
    if( $this->getRequest()->isPost() ) {
      $global_directory_name = APPLICATION_PATH . '/application/themes/sitecoretheme';
      @chmod($global_directory_name, 0777);

      if( !is_readable($global_directory_name) ) {
        $this->view->error_message = "<span style='color:red'>Note: You do not have readable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
Path Name: <b>" . $global_directory_name . "</b></span>";
        return;
      }

      $global_settings_file = $global_directory_name . '/customization.css';
      $is_file_exist = @file_exists($global_settings_file);
      if( empty($is_file_exist) ) {
        @chmod($global_directory_name, 0777);
        if( !is_writable($global_directory_name) ) {
          $this->view->error_message = "<span style='color:red'>Note: You do not have writable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
Path Name: " . $global_directory_name . "</span>";
          return;
        }

        $fh = @fopen($global_settings_file, 'w');
        @fwrite($fh, '/* ADD CUSTOM STYLE */');
        @fclose($fh);

        @chmod($global_settings_file, 0777);
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('File Succesfully Created.')
      ));
    }
  }

  //ACTION FOR MULTI-DELETE OF IMAGES
  public function multiDeleteAction()
  {

    if( $this->getRequest()->isPost() ) {

      $values = $this->getRequest()->getPost();

      foreach( $values as $key => $value ) {

        if( $key == 'delete_' . $value ) {

          $item = Engine_Api::_()->getItem('sitecoretheme_image', $value);

          $item->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'landing-images'));
  }

  //ACTION FOR MULTI-DELETE OF BANNERS
  public function multiDeleteBannersAction()
  {

    if( $this->getRequest()->isPost() ) {

      $values = $this->getRequest()->getPost();

      foreach( $values as $key => $value ) {

        if( $key == 'delete_' . $value ) {

          $item = Engine_Api::_()->getItem('sitecoretheme_banner', $value);

          $item->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'banners'));
  }

  public function customCssAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_theme_custom');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_theme_custom', array(), 'sitecoretheme_admin_settings_custom_css');
    $filePath = APPLICATION_PATH . '/application/themes/sitecoretheme/customization.css';
    $isFileExist = @file_exists($filePath);

    $this->view->message = '';
    $this->view->sucess = false;

    if( empty($isFileExist) ) {
      $fh = @fopen($filePath, 'w') or die('Unable to write customization CSS file; please give the CHMOD 777 recursive permission to the directory /application/themes/sitecoretheme/, then try again.');
      @fclose($fh);
    } elseif( !is_writable($filePath) ) {
      @chmod($filePath, 0777);
      if( !is_writable($filePath) ) {
        $this->view->message = 'Unable to write customization CSS file; please give the CHMOD 777 recursive permission to the directory /application/themes/sitecoretheme/, then try again.';
        return;
      }
    }

    $this->view->fileContent = file_get_contents($filePath);

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !isset($_POST['sitecoretheme_custom_css']) ) {
      return;
    }

    if( @file_put_contents($filePath, $_POST['sitecoretheme_custom_css']) ) {
      @chmod($filePath, 0777);
      $this->view->message = 'Your changes have been saved.';
      $this->view->sucess = true;
    }
    $this->view->fileContent = $_POST['sitecoretheme_custom_css'];
  } 
}