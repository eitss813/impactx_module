<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Custom_Fields extends Fields_Form_Standard {

  public $_error = array();
  protected $_name = 'fields';
  protected $_elementsBelongTo = 'fields';

  public function init() {
      // custom sitepage fields
      if (!$this->_item) {
        $sitepage_item = new Sitepage_Model_Page(array());
        $this->setItem($sitepage_item);
      }
      parent::init();

      $this->removeElement('submit');
  }

  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('FormElements');
    }
  }

}

?>