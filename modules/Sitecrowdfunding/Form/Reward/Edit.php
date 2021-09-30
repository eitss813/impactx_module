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
class Sitecrowdfunding_Form_Reward_Edit extends Sitecrowdfunding_Form_Reward_Create {

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

        parent::init();
        $this->setTitle("Edit Reward Information")
                ->setDescription("Edit the information of your Reward using the form below.");
        $this->execute->setLabel('Save Changes');

        $this->pledge_amount->setRequired(false);
        $this->shipping_method->setRequired(false);

    }

}
