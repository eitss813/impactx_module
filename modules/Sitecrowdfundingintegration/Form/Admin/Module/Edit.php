<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2015-1-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfundingintegration_Form_Admin_Module_Edit extends Sitecrowdfundingintegration_Form_Admin_Module_Add {

    public function init() {

        parent::init();
        $this
                ->setTitle('Edit Module')
                ->setDescription('Edit various details associated with the integrated content module.');
    }

}
