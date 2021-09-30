<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLandingPageController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_AdminLandingPageController extends Core_Controller_Action_Admin
{

  public function sliderAction()
  {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_slider');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Slider();

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
      $coreSettings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function ctaButtonsAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_cta');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_CtaButtons();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->saveValuesWithIcons($form, 'sitecoretheme_ctabutton');
  }

  public function statsAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_stats');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Stats();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->saveValuesWithIcons($form, 'sitecoretheme_stat');
  }
  
    public function markersAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_markers');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Markers();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->saveValuesWithIcons($form, 'sitecoretheme_markers', 200);
  }

  public function textBannerAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_text_banner');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_TextBanner();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->saveValues($form);
  }

  public function appBannerAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_app_banner');

    //MAKE FORM
    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_AppBanner();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $this->saveValues($form);
  }

  public function servicesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_services');
    $this->view->services = Engine_Api::_()->getDbtable('services', 'sitecoretheme')->getServices();
  }

  public function addServicesAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Services(array('iconRequired' => true));
    $form->setAction($this->view->url(array()));

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }

    // Process
    $values = $form->getValues();

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('services', 'sitecoretheme');
    $db = $table->getAdapter();
    $db->beginTransaction();
    // Update row
    try {
      $services = $table->createRow();
      $services->title = $values['title'];
      $services->description = $values['description'];
      if( !empty($values['icon']) ) {
        $services->setPhoto($form->icon);
      }

      $services->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
    ));
  }

  public function deleteServiceAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $serviceId = $this->_getParam('id');
    $this->view->service_id = $serviceId;
    $servicesTable = Engine_Api::_()->getDbtable('services', 'sitecoretheme');
    $service = $servicesTable->find($serviceId)->current();

    $serviceId = $service->getIdentity();

    if( !$serviceId ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Entry doesn't exist or not authorized to delete");
      return;
    }

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Delete();

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    // Process
    $db = $servicesTable->getAdapter();
    $db->beginTransaction();

    try {

      $service->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your entry has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitecoretheme', 'controller' => 'landing-page', 'action' => 'services'), "admin_default", true),
        'messages' => Array($this->view->message)
    ));
  }

  public function editServiceAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $serviceId = $this->_getParam('id');
    $serviceTable = Engine_Api::_()->getDbtable('services', 'sitecoretheme');
    $serviceRow = $serviceTable->find($serviceId)->current();
    //  $service = Engine_Api::_()->getItem('sitemusic_artist', $serviceId);
    if( !$serviceRow ) {
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    } else {
      $serviceId = $serviceRow->getIdentity();
    }

    $form = $this->view->form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Services();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->populate($serviceRow->toArray());

    if( !$this->getRequest()->isPost() ) {
      // Output
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      // Output
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = $serviceTable->getAdapter();
    $db->beginTransaction();

    try {
      $serviceRow->title = $values['title'];
      $serviceRow->description = $values['description'];
      if( !empty($values['icon']) ) {
        $serviceRow->setPhoto($form->icon);
      }
      $serviceRow->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
    ));
  }

  public function enableServiceAction()
  {
    $id = $this->_getParam('id');
    $enable = $this->_getParam('enable');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $artistTable = Engine_Api::_()->getItem('sitecoretheme_service', $id);
      $artistTable->enabled = $enable;
      $artistTable->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitecoretheme/landing-page/services');
  }

  public function highlightsAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_highlights');

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_Highlights();
    $this->view->selectedMenuType = 'view';
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    if( $values['sitecoretheme_landing_highlights_attachVideo'] && empty($values['sitecoretheme_landing_highlights_videoEmbed']) ) {
      $form->addError('Please enter video embed code');
      return;
    }
    foreach( $values as $key => $value ) {
      $coreSettings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function videoBannerAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_videobanner');

    $this->view->form = $form = new Sitecoretheme_Form_Admin_Settings_Landingpage_VideoBanner();
    $this->view->selectedMenuType = 'view';
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    if( empty($values['sitecoretheme_landing_videobanner_videoType']) && empty($values['sitecoretheme_landing_videobanner_videoEmbed']) ) {
      $form->addError('Please enter video embed code');
      return;
    }
    if( !empty($values['sitecoretheme_landing_videobanner_videoType']) && empty($values['sitecoretheme_landing_videobanner_videoUrl']) ) {
      $form->addError('Please enter video url');
      return;
    }
    foreach( $values as $key => $value ) {
      if( $coreSettings->hasSetting($key) ) {
        $coreSettings->removeSetting($key);
      }
      $coreSettings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function listHighlightsAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_main', array(), 'sitecoretheme_admin_landingpage');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecoretheme_admin_landingpage', array(), 'sitecoretheme_admin_landingpage_highlights');
    $this->view->highlights = Engine_Api::_()->getDbtable('highlights', 'sitecoretheme')->getHighlights();

    $this->view->selectedMenuType = 'edit';
    $enabledHighlights = Engine_Api::_()->getDbtable('highlights', 'sitecoretheme')->getHighlights(array('enabled' => 1));
    $this->view->minimumHighlight = false;
    $this->view->oddHightLights = false;
    $this->view->message = '';
    if( count($enabledHighlights) < 4 ) {
      $this->view->minimumHighlight = true;
      $this->view->message = 'Add 4 or more Blocks in order to get similar yet admirable visual of this section.';
    } else if( count($enabledHighlights) >= 4 && count($enabledHighlights) % 2 != 0 ) {
      $this->view->oddHightLights = true;
      $this->view->message = 'Add Blocks even in number in order to get similar yet admirable visual of this section.';
    }
  }

  public function editHighlightAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $highlightId = $this->_getParam('id');
    $highlightTable = Engine_Api::_()->getDbtable('highlights', 'sitecoretheme');
    $highlightRow = $highlightTable->find($highlightId)->current();

    if( !$highlightRow ) {
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    } else {
      $highlightId = $highlightRow->getIdentity();
    }

    $form = $this->view->form = new Sitecoretheme_Form_Admin_Settings_Landingpage_EditHighlights();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->populate($highlightRow->toArray());

    if( !$this->getRequest()->isPost() ) {
      // Output
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      // Output
      $this->renderScript('admin-landing-page/form.tpl');
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = $highlightTable->getAdapter();
    $db->beginTransaction();

    try {
      $highlightRow->title = $values['title'];
      $highlightRow->description = $values['description'];
      if( !empty($values['icon']) ) {
        $highlightRow->setPhoto($form->icon);
      }

      $highlightRow->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
    ));
  }

  public function enableHighlightAction()
  {
    $id = $this->_getParam('id');
    $enable = $this->_getParam('enable');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $artistTable = Engine_Api::_()->getItem('sitecoretheme_highlight', $id);
      $artistTable->enabled = $enable;
      $artistTable->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitecoretheme/landing-page/list-highlights');
  }

  public function saveValues($form)
  {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    foreach( $values as $key => $value ) {
      $coreSettings->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function saveValuesWithIcons($form, $type = null, $size = 64)
  {
    $iconFields = array('sitecoretheme_landing_side-banner','sitecoretheme_landing_cta_icon1', 'sitecoretheme_landing_cta_icon2', 'sitecoretheme_landing_cta_icon3', 'sitecoretheme_landing_cta_hover_icon1', 'sitecoretheme_landing_cta_hover_icon2', 'sitecoretheme_landing_cta_hover_icon3', 'sitecoretheme_landing_stats_icon1', 'sitecoretheme_landing_stats_icon2', 'sitecoretheme_landing_stats_icon3', 'sitecoretheme_landing_stats_icon4', 'sitecoretheme_landing_markers_icon1', 'sitecoretheme_landing_markers_icon2', 'sitecoretheme_landing_markers_icon3', 'sitecoretheme_landing_markers_icon4');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    foreach( $values as $key => $value ) {
      if( in_array($key, $iconFields) ) {
        if( !empty($value) ) {
          $size = $key == 'sitecoretheme_landing_side-banner' ? 600 : $size;
          $file_id = $this->setPhoto($form->{$key}, null, $type, $size);
          if( $coreSettings->hasSetting($key) ) {
            $coreSettings->removeSetting($key);
          }
          $coreSettings->setSetting($key, $file_id);
        }
      } else {
        if( $coreSettings->hasSetting($key) ) {
          $coreSettings->removeSetting($key);
        }
        $coreSettings->setSetting($key, $value);
      }
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function setPhoto($photo, $parent_id = null, $parent_type = null, $size = 64)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Engine_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $thumb_file = $path . '/in_' . $name;
    $image = Engine_Image::factory();

    $image->open($file)
      ->resize($size, $size)
      ->write($thumb_file)
      ->destroy();
    try {
      $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
        'parent_type' => $parent_type,
        'parent_id' => $parent_id
      ));
      // Remove temp file
      @unlink($thumb_file);
    } catch( Exception $e ) {
      
    }

    $file_id = $thumbFileRow->file_id;
    return $file_id;
  }

}