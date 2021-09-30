<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_FeaturedownerSitepageController extends Seaocore_Content_Widget_Abstract {
  protected $_childCount;
  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    //$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    $manageadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
    if (empty($manageadmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//FETCH FEATURED ADMIN
    $this->view->featuredowners = $featuredowners = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->featuredAdmins($sitepage->page_id);
		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			// Set item count per page and current page number
			$this->view->featuredowners = $featuredowners->setItemCountPerPage(5);
			$this->view->featuredowners = $featuredowners->setCurrentPageNumber($this->_getParam('page', 1));

			// Add count to title if configured
			if ($this->_getParam('titleCount', false) && $featuredowners->getTotalItemCount() > 0) {
				$this->_childCount = $featuredowners->getTotalItemCount();
			}

      if ($featuredowners->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}

    } else {
			if (!count($featuredowners)) {
				return $this->setNoRender();
			}
    }

  }

	public function getChildCount() {
    return $this->_childCount;
  }
}

?>