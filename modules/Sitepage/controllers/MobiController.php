<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_MobiController extends Core_Controller_Action_Standard {

  protected $_navigation;

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

		//PAGE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
        ->addActionContext('rate', 'json')
        ->addActionContext('validation', 'html')
        ->initContext();

		//GET PAGE URL AND PAGE ID
    $page_url = $this->_getParam('page_url', $this->_getParam('page_url', null));
    $page_id = $this->_getParam('page_id', $this->_getParam('page_id', null));

    if ($page_url) {
      $id = Engine_Api::_()->sitepage()->getPageId($page_url);
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    } elseif ($page_id) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }
 
		//FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
      Engine_Api::_()->sitepage()->updateExpiredPages();
    }
  }

  //ACTION FOR SHOWING THE PAGE LIST
  public function indexAction() {

		//PAGE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME PAGE
  public function homeAction() {

		//PAGE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

	//ACTION FOR VIEW PROFILE PAGE
  public function viewAction() {

		//RETURN IF SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET PAGE SUBJECT AND THEN CHECK VALIDATION
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if (empty($sitepage)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $page_id = $sitepage->page_id;

    $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);

    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);

    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    if ($memory_Size_int <= 32)
      ini_set('memory_limit', '64M');
    $maxView = 19;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!Engine_Api::_()->sitepage()->canViewPage($sitepage)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

		$current_date = date('Y-m-d H:i:s');
    $this->view->headScript()->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/core.js')  
                             ->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/hideTabs.js');
//     $sitepagetable = Engine_Api::_()->getDbtable('vieweds', 'sitepage');
// 
// 		//FUNCTION CALLING AND PASS PAGE ID AND VIEWER ID
// 		$sitepageresult = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->getVieweds($viewer_id, $page_id);
// 
//     $count = count($sitepageresult);
//     if (empty($count)) {
//       $row = $sitepagetable->createRow();
//       $row->page_id = $page_id;
//       $row->viewer_id = $viewer_id;
//       $row->save();
//     } else {
//       $sitepagetable->update(array('date' => $current_date), array('page_id = ?' => $page_id, 'viewer_id' => $viewer_id));
//     }

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitepage->getOwner();
   
    $sub_status_table = Engine_Api::_()->getDbTable('pagestatistics', 'sitepage');
   
		//INCREMENT PAGE VIEWS DATE-WISE
    $values = array('page_id' => $sitepage->page_id);

    $statObject = $sub_status_table->pageReportInsights($values);
    $raw_views = $sub_status_table->fetchRow($statObject);
    $raw_views_count = $raw_views['views'];
    if (!$owner->isSelf($viewer) || ($sitepage->view_count == 1 && empty($raw_views_count))) {
      $sub_status_table->pageViewCount($page_id);
    }

    if (!$owner->isSelf($viewer)) {
      $sitepage->view_count++;
    }

    $sitepage->save();

		//CHECK TO SEE IF PROFILE STYLES IS ALLOWED
    $style_perm = 1;

    if ($style_perm) {

      //GET STYLE
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
              ->where('type = ?', $sitepage->getType())
              ->where('id = ?', $sitepage->getIdentity())
              ->limit();

      $row = $table->fetchRow($select);
      if (null !== $row && !empty($row->style)) {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

    if (null !== ($tab = $this->_getParam('tab'))) {
      $friend_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
      																		if(window.tabContainerSwitch) 
      																		{
                                              tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
																					}
                                        }
EOF;
      $this->view->headScript()->appendScript($friend_tab_function);
    }    
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
  
		// Start: Suggestion work.
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    // Here we are delete this poll suggestion if viewer have.
    if (!empty($is_moduleEnabled)) {
      Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer->getIdentity(), 'sitepage_page', $page_id, 'page_suggestion');
    }
    // End: Suggestion work.
    
		if ($coreversion < '4.1.0') {

			$this->_helper->content->render();
		} else {
			$this->_helper->content->setNoRender()->setEnabled();
		}
  }
}
?>