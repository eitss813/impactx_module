<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Edit extends Sitecrowdfunding_Form_Project_Create {

    public $_error = array();
    protected $_item;
    protected $_defaultProfileId;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init() {

        parent::init();
        $this->setTitle("Edit Project Info")
                ->setDescription("Edit the information of your project using the form below.");

        if ($this->location)
            $this->removeElement('location');
        
        $this->execute->setLabel('Save Changes');
    }

}
