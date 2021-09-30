<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Widget_LocationsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
      $this->view->height = $height = $this->_getParam('height', '200');
      $this->view->lng = $lng = $this->_getParam('lng', '');
      $this->view->lat = $lat = $this->_getParam('lat', '');
			$this->view->mapzoom = $lat = $this->_getParam('mapzoom', '14');
			if(!$lat || !$lng)
				$this->setNoRender();
      $this->view->location = $location = $this->_getParam('location', '');
			$this->view->quickContact = $quickContact = $this->_getParam('quickContact', '');
			if($quickContact){
				$this->view->aboutdescr = $this->_getParam('aboutdescr', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.');

				$this->view->address = $this->_getParam('address', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.');

				$this->view->email = $this->_getParam('email', 'info@example.com');
				$this->view->phone = $this->_getParam('phone', '(+2)00000000000');
				$this->view->skype = $this->_getParam('skype', 'skype.id');
				$this->view->company = $this->_getParam('company', '');
				$this->view->facebook = $this->_getParam('facebook', 'http://www.facebook.com');
				$this->view->twitter = $this->_getParam('twitter', 'http://www.twitter.com');
				$this->view->youtube = $this->_getParam('youtube', 'http://www.youtube.com');
				$this->view->linkdin = $this->_getParam('linkdin', 'http://www.linkedin.com');
				$this->view->googleplus = $this->_getParam('googleplus', 'http://www.google.com');
				$this->view->rssfeed = $this->_getParam('rssfeed', 'http://feeds.feedburner.com');
				$this->view->pinterest = $this->_getParam('pinterest', 'http://www.pinterest.com');
			}
  }
}