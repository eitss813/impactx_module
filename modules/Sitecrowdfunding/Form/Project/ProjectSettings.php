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
class Sitecrowdfunding_Form_Project_ProjectSettings extends Engine_Form {

    public function init() {

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $this->setTitle("Edit Project Profile Settings")
                ->setDescription("You can configure below settings related to the your Project’s profile page")
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Project Settings:
        $this->addElement('Radio', 'profile_cover', array(
            'label' => 'Main Photo / Video',
            'description' => "What do you want to show as main content, photo / video, on your Project’s profile page?<br>[Note: You can choose the main photo / video from the ‘Photos’ / ‘Videos’ section of the dashboard.]",
            'multiOptions' => array(
                '1' => 'Photo',
                '0' => 'Video',
            ),
            'value' => 1,
        ));
        $this->profile_cover->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
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
        }


        // Photo Change
        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(
                array('ViewScript', array(
                    'viewScript' => '_formEditImage.tpl',
                    'class' => 'form_element',
                    'testing' => 'testing'
                )
                ),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'form-label')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'form-wrapper sitecrowdfunding_profile_picture_wrapper' , 'id' => 'current-wrapper')),
            ),
        ));
        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'id' => 'file_browse',
            'decorators' => array(
                'File',
                array('HtmlTag', array('tag' => 'div' , 'id' => 'file_browse_wrapper' )),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'form-label')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'form-wrapper file-box' , 'id' => 'Filedata-wrapper')),
            ),
        ));
        $this->addElement('Hidden', 'coordinates', array(
            'filters' => array(
                'HtmlEntities',
            )
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'remove-photo', 'project_id' => $project_id), "sitecrowdfunding_dashboard", true);
        if ($project->photo_id != 0) {
            $this->addElement('Button', 'remove', array(
                'label' => 'Remove Photo',
                'onclick' => "removePhotoProject('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
            $url = $view->url(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view", true);
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' ' . Zend_Registry::get('Zend_Translate')->_('or') . ' ',
                'link' => true,
                'onclick' => "removePhotoProject('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
            $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
        }


        // Pic Change
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Settings',
            'type' => 'submit',
        ));

    }

}
