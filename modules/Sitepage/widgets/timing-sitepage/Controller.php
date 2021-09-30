<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_TimingSitepageController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		// check extension installed or not
		$featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
		if (!$featureExtension) {
			return $this->setNoRender();
		}
		$element = $this->getElement();
		$this->view->widgetTitle = $element->getTitle();
		$element->setTitle('');
		$timing_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.operating.hours.enable', 0);
		if (!$timing_enable) {
			return $this->setNoRender();
		}
        //GET PAGE ID
		$sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
		$page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
		$this->view->timezone = $page_owner->timezone;
		$this->view->page_id = $page_id = $sitepage->page_id;

		$this->view->online_status = Engine_Api::_()->sitepage()->isOnline($sitepage);

        //GET SITEPAGE ITEM
		if($sitepage->days == 0) {
			$table = Engine_Api::_()->getItemTable('sitepage_timing');
			$select = $table->select()->where('page_id = ?',$page_id);
			$this->view->row = $row=$table->fetchAll($select);
		}
		else {
			$this->view->status = true;
		}
	}

}

?>