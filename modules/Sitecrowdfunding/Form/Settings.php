<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Settings extends Engine_Form {

    public function init() {

        $this->setTitle("Edit Project Profile Settings")
                ->setDescription("You can configure below settings related to the your Project’s profile page.")
                ->setAttrib('name', 'owner_settings');

        $this->addElement('Radio', 'profile_cover', array(
            'label' => 'Please upload a main project profile photo or video?',
            'multiOptions' => array(
                1 => 'Photo',
                0 => 'Video',
            ),
            'value' => 1,
            'required' => true,
            'onchange' => 'checkIsProfileCover(this.value);'
        ));
        $this->profile_cover->getDecorator('Description')->setOption('placement', 'append');


        // Enable join and approve
        /*
        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.invite.option', 1);
        $memberApproval = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.member.approval.option', 1);
        if(!empty($memberInvite) ) {
            $this->addElement('Radio', 'member_invite', array(
                'label' => 'Invite member',
                'multiOptions' => array(
                    '0' => 'Yes, members can invite other people.',
                    '1' => 'No, only project admins can invite other people.',
                ),
                'value' => '1',
                'attribs' => array('class' => 'sp_quick_advanced')
            ));
        }
        if(!empty($memberApproval) ) {
            $this->addElement('Radio', 'member_approval', array(
                'label' => 'Approve members?',
                'description' => ' When people try to join this project, should they be allowed ' .
                    'to join immediately, or should they be forced to wait for approval?',
                'multiOptions' => array(
                    '0' => 'New members must be approved.',
                    '1' => 'New members can join immediately.',
                ),
                'value' => '1',
            ));
        }*/

        $this->addElement('Hidden', 'form_type', array('value' => 'edit_settings','order' => 800000));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Settings',
            'type' => 'submit',
        ));
    }

}
