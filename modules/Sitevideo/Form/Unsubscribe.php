<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Unsubscribe.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Unsubscribe extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Unsubscribe Channel')
                ->setDescription('Are you sure you want to unsubscribe this channel?')
                ->setMethod('POST')
                ->setAction($_SERVER['REQUEST_URI'])
                ->setAttrib('class', 'global_form_popup')
        ;

        $this->addElement('Button', 'execute', array(
            'label' => 'Unsubscribe Channel',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel'
                ), 'buttons');
    }

}
