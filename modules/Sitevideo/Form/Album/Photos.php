<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Album_Photos extends Engine_Form {

    public function init() {

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
        ));
    }

}
