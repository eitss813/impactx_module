<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Widget_footerController extends Engine_Content_Widget_Abstract {

  public function indexAction() {


    $settings = Engine_Api::_()->getApi('settings', 'core');

  	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_footer');
    $this->view->fotrbgcolor = $settings->getSetting('sesnewsletter.fotrbgcolor', 'FFF');
    $this->view->footerfontcolor = $settings->getSetting('sesnewsletter.footerfontcolor', '000');
    $this->view->footermenu = $settings->getSetting('sesnewsletter.footermenu', 1);
    $this->view->fotrenablelogo = $settings->getSetting('sesnewsletter.fotrenablelogo', '0');
    $this->view->fotrlogositetext = $settings->getSetting('sesnewsletter.fotrlogositetext', $settings->getSetting('core.general.site.title', ''));
    $this->view->fotrlogo = $settings->getSetting('sesnewsletter.fotrlogo', '');

    $this->view->facebook = $settings->getSetting('sesnewsletter.facebook', '');
    $this->view->twitter = $settings->getSetting('sesnewsletter.twitter', '');
    $this->view->youtube = $settings->getSetting('sesnewsletter.youtube', '');
    $this->view->websiteurl = $settings->getSetting('sesnewsletter.websiteurl', '');
  }
}
