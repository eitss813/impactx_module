<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Watchlater_Delete extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Remove Video')
                ->setDescription('Are you sure you want to remove this video from watch later ?')
                ->setMethod('POST')
                ->setAction($_SERVER['REQUEST_URI'])
                ->setAttrib('class', 'global_form_popup')
        ;

        $this->addElement('Button', 'execute', array(
            'label' => 'Remove Video',
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
