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

class Sesnewsletter_Widget_HeaderController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_header');
    $this->view->tophebgcolor = $settings->getSetting('sesnewsletter.tophebgcolor', '000');
    $this->view->topheaderfontcolor = $settings->getSetting('sesnewsletter.topheaderfontcolor', 'FFF');
    $this->view->hebgcolor = $settings->getSetting('sesnewsletter.hebgcolor', 'FFF');
    $this->view->headerfontcolor = $settings->getSetting('sesnewsletter.headerfontcolor', '000');
    $this->view->headermenu = $settings->getSetting('sesnewsletter.headermenu', 1);
    $this->view->enablelogo = $settings->getSetting('sesnewsletter.enablelogo', '0');
    $this->view->logositetext = $settings->getSetting('sesnewsletter.logositetext', $settings->getSetting('core.general.site.title', ''));
    $this->view->helogo = $settings->getSetting('sesnewsletter.helogo', '');
    $this->view->phonenumber = $settings->getSetting('sesnewsletter.phonenumber', '');
    $this->view->email = $settings->getSetting('sesnewsletter.email', '');

    $this->view->facebook = $settings->getSetting('sesnewsletter.facebook', '');
    $this->view->twitter = $settings->getSetting('sesnewsletter.twitter', '');
    $this->view->linkedin = $settings->getSetting('sesnewsletter.linkedin', '');
    $this->view->pinterest = $settings->getSetting('sesnewsletter.pinterest', '');


  }
}
