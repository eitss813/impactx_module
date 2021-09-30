<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Form_Admin_Categories_Edit extends Sitepage_Form_Admin_Categories_Add {

  public function init() {
    parent::init();

    $this->submit->setLabel('Save Changes');
  }

}