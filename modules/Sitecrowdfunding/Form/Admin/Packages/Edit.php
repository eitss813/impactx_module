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
class Sitecrowdfunding_Form_Admin_Packages_Edit extends Sitecrowdfunding_Form_Admin_Packages_Create {

    public function init() {
        parent::init();

        $this->setTitle('Edit Package')
                ->setDescription('Edit your project package over here. Below, you can configure various settings for this package like video, overview, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

        // Disable some elements
        $this->getElement('price')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;

        $this->getElement('recurrence')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;
        $this->getElement('duration')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;
        $this->removeElement('trial_duration');

        // Change the submit label
        $this->getElement('execute')->setLabel('Edit Package');
    }

}
