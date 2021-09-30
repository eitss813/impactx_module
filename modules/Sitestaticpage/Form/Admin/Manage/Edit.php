<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Form_Admin_Manage_Edit extends Sitestaticpage_Form_Admin_Manage_Create {

  public function init() {
    parent::init();

    $this->setTitle('Edit Static Page / HTML Block')->setDescription("Edit Your Static Page / HTML Block here. Please note that, if this page is already a widgetized page, then it can not be converted into a Non-widgetized page. Here, you can convert a HTML Block into a Static Page by adding some text in the URL component.");
    $this->submit->setLabel('Edit');
  }

}