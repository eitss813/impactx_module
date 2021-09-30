<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ManageCategorySettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class sitecrowdfunding_Form_Admin_Settings_ManageMember extends Engine_Form {

  public function init() {

    $this->setTitle('Manage Member');

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

      // automatically invite others options: This is set in project edit/create page
      /*
    // Automatically Add People
    $this->addElement( 'Radio' , 'sitecrowdfunding_member_automatically_addmember' , array (
      'label' => 'Automatically Add People',
      'description' => "Do you want people to be automatically added to a project when project admins or other members of that project add them? (Note: This setting will not work for the project which need admin approval when people try to join that project.)",
      'multiOptions' => array (
          1 => 'Yes' ,
          0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.automatically.addmember' , 1),
    )) ;
      */

      // Enable member invite others options
      $this->addElement( 'Radio' , 'sitecrowdfunding_member_invite_option' , array (
          'label' => 'Enable “Member Invite Others” Option',
          'description' => "Do you want to enable “Member Invite Others” option in the projects using which users will be able to choose who should be able to invite other people to their projects? ",
          'multiOptions' => array (
              1 => 'Yes' ,
              0 => 'No'
          ) ,
          'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.invite.option' , 1),
          // 'onclick' => 'showInviteOption(this.value)',
      ));

      // member invite others options: This is set in project edit/create page
      /*
      $this->addElement('Radio', 'sitecrowdfunding_member_invite_automatically', array(
          'label' => 'Member Invite Others',
          'description' => 'Do you want project members to invite other people to the projects they join?',
          'multiOptions' => array(
              '0' => 'Yes, members can invite other people.',
              '1' => 'No, only project admins can invite other people',
          ),
          'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.invite.automatically' , 1),
      ));
      */

      $this->addElement( 'Radio' , 'sitecrowdfunding_member_approval_option' , array (
          'label' => 'Enable “Approve Members” Option',
          'description' => "Do you want to enable “Approve Members” option in the projects using which project admins will be able to choose that when people try to join projects, should they be allowed to join immediately, or should they be forced to wait for approval?",
          'multiOptions' => array (
              1 => 'Yes' ,
              0 => 'No'
          ) ,
          'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.approval.option' , 1),
          // 'onclick' => 'showApprovalOption(this.value)'
      ));

      // Approve Members others options: This is set in project edit/create page
      /*
      $this->addElement('Radio', 'sitecrowdfunding_member_approval_automatically', array(
          'label' => 'Approve Members',
          'description' => 'When people try to join the projects on your site, should they be allowed to join immediately, or should they be forced to wait for approval?',
          'multiOptions' => array(
              '1' => 'New members can join immediately.',
              '0' => 'New members must be approved.',
          ),
          'value' => $coreSettings->getSetting( 'sitecrowdfunding.member.approval.automatically' , 1),
      ));
      */

    $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
      ));
  }
}