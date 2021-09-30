<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Edit extends Sitepage_Form_Create {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    // call the init of create form


    parent::init();
    $sitepage = $this->getItem();

    $this->setTitle('Edit Page Info')
            ->setDescription('Edit the information of your page and keep it updated.');
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) && !empty($sitepage->category_id)) {
      $this->getElement('category_id')
              ->setIgnore(true)
              ->setAttrib('disable', true)
              ->clearValidators()
              ->setRequired(false)
              ->setAllowEmpty(true)
      ;
    }
    
    if ($this->location)
      $this->removeElement('location');
      
    $this->execute->setLabel('Save Changes');
  }

}

?>