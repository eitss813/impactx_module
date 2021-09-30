<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Widget_ListMembersTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    $itemCount = $this->_getParam('itemCount', 10);

		$this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
		
		//GET THE BASE URL.
		$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		
    if (empty($is_ajax)) {
      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepagemember', 'type' => 'member', 'enabled' => 1));
      $count_tabs = count($tabs);
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }
      $activeTabName = $tabs[0]['name'];
    }
    
    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepagemember', 'type' => 'member', 'enabled' => 1, 'name' => $activeTabName));
    $this->view->activTab = $activTab = $activeTab['0'];

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->listMemeberTabWidget($activTab);

    $paginator->setItemCountPerPage($itemCount);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount(); 
  }
}