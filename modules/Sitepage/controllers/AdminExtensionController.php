<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_AdminExtensionController extends Core_Controller_Action_Admin
{
	public function indexAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_extension');
	}
	public function upgradeAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_extension');
	}

  public function informationAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_extension');
  }

  public function deletemoduleAction() {

    //GET MODULE NAME
    $moduleName = $this->_getParam('modulename');
    $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$moduleName");
    $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
    if(!empty($resultMenuitems->enabled)) {
    $name = $resultMenuitems->name;
    $menuitemsTable->update(array('enabled' => '0')
                , array(
            'name =?' => $name
        ));
    }
    else {
      $name = $resultMenuitems->name;
      $menuitemsTable->update(array('enabled' => '1')
                , array(
            'name =?' => $name
        ));
    }
    $this->_redirect('admin/sitepage/extension');
  }

}
?>