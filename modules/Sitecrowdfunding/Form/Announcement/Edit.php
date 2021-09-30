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
class Sitecrowdfunding_Form_Announcement_Edit extends Sitecrowdfunding_Form_Announcement_Create {

    public function init() {

        parent::init();
        $this->setTitle('Edit Announcement')
                ->setDescription('Edit the announcement for your project below.');

        // Change the submit label
        $this->getElement('submit')->setLabel('Edit Announcement');
    }

}