<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditRole.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Settings extends Engine_Form
{

    protected $_field;

    public function init()
    {

        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id');
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('package_id');

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitepages_edit_settings');

        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.title', 1);
        if (!empty($memberTitle)) {
            $this->addElement('Text', 'member_title', array(
                'label' => 'What will members be called?',
                'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                )));
            $this->member_title->getDecorator('Description')->setOption('placement', 'append');
        }


        $this->addElement('Radio', 'member_invite', array(
            'label' => 'Invite member',
            'multiOptions' => array(
                '0' => 'Yes, members can invite other people.',
                '1' => 'No, only page admins can invite other people.',
            ),
            'value' => '1',
            'attribs' => array('class' => 'sp_quick_advanced')
        ));


        $this->addElement('Radio', 'member_approval', array(
            'label' => 'Approve members?',
            'description' => ' When people try to join this page, should they be allowed ' .
                'to join immediately, or should they be forced to wait for approval?',
            'multiOptions' => array(
                '1' => 'New members can join immediately.',
                '0' => 'New members must be approved.',
            ),
            'value' => '1',
        ));


        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

    }
}