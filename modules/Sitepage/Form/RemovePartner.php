<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Demote.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_RemovePartner extends Engine_Form
{

    public function init()
    {
        $this
            ->setTitle('Remove sister pages')
            ->setDescription('Are you sure you want to remove this sister pages ?');

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'label' => 'Remove',
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'Cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }

}