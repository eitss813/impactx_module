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
class Sitepage_Form_Admin_Package_Edit extends Sitepage_Form_Admin_Package_Create {

  public function init() {
    parent::init();

    $this
            ->setTitle('Edit Page Package')
            ->setDescription('Edit your page package over here. Below, you can configure various settings for this package like tell a friend, overview, map, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.')
    ;

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

?>