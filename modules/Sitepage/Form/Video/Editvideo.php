<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editvideo.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Video_Editvideo extends Engine_Form {

    public function init() {

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->addElement('Radio', 'cover', array(
            'label' => 'Video Cover',
        ));
        $this->addElement('Button', 'button', array(
            'label' => 'Save Videos',
            'type' => 'submit',
        ));
    }

}