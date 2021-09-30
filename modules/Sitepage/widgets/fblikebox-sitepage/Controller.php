<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Widget_FblikeboxSitepageController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
        
				//DON'T RENDER IF SUNJECT IS NOT THERE
				if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postfbpage', 1)) {
					return $this->setNoRender();
				}
				
				//GET SITEPAGE SUBJECT
				$this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page'); 
				//print_r($sitepage);die;
				if (!$sitepage || empty($sitepage->fbpage_url)) {
					return $this->setNoRender();
				}
				
				$this->view->fbpage_url = $sitepage->fbpage_url;
      //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
			$LikeboxSetting['fb_width'] = $this->_getParam('fb_width', '190');
			$LikeboxSetting['fb_height'] = $this->_getParam('fb_height', '588');
			$LikeboxSetting['border_color'] = $this->_getParam('widget_border_color', '');
			$LikeboxSetting['widget_color_scheme'] = $this->_getParam('widget_color_scheme', 'light');
			$LikeboxSetting['widget_show_faces'] = $this->_getParam('widget_show_faces', array('0' => 'true'));
			if (empty($LikeboxSetting['widget_show_faces'][0])) 
					$LikeboxSetting['widget_show_faces'] = 'false';
			else
				$LikeboxSetting['widget_show_faces'] = 'true';
			$LikeboxSetting['show_stream'] = $this->_getParam('show_stream', array('0' => 'true'));
			if (empty($LikeboxSetting['show_stream'][0]))
			    $LikeboxSetting['show_stream'] = 'false';
			else
				$LikeboxSetting['show_stream'] = 'true';		
			$LikeboxSetting['show_header'] = $this->_getParam('show_header', array('0' => 'true'));
			if (empty($LikeboxSetting['show_header'][0]))
			    $LikeboxSetting['show_header'] = 'false';
			else
				$LikeboxSetting['show_header'] = 'true';    
			$this->view->LikeboxSetting = $LikeboxSetting;

	}
}
