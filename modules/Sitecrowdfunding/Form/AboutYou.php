<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AboutYou.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_AboutYou extends Engine_Form {

    public $_error = array();

    public function init() {

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitecrowdfunding_general', true);
        }

        $this->setTitle("About You")
                ->setDescription("Fill the form below to give complete information about yourself. By creating rich and detailed biography will help the members to build trust on the authenticity of the project owner and the project. Thus, this will speed up the backing process of your project.")
                ->setAttrib('name', 'about_you');
        $this->addElement('textarea', 'biography', array(
            'label' => "Biography",
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));

        $allowContact = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "contact");
        if ($allowContact) {
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $contactDetailArray = $coreSettings->getSetting('sitecrowdfunding.contactdetail', array('social_media', 'website', 'email'));
            if (empty($contactDetailArray)) {
                $contactDetailArray = array();
            }
            if (in_array('email', $contactDetailArray)) {

                $this->addElement('Text', 'email', array(
                    'label' => 'Email Address',
                    'validators' => array(
                        array('EmailAddress', true)
                    ),
                ));
            }
            if (in_array('phone', $contactDetailArray)) {
                $this->addElement('Text', 'phone', array(
                    'label' => 'Phone',
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ));
            }
            if (in_array('social_media', $contactDetailArray)) {
                $this->addElement('Dummy', 'profileHeading', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_formElementsHeading.tpl',
                                'heading' => Zend_Registry::get('Zend_Translate')->_("Your Social Media Profile URL's"),
                                'class' => 'form element'
                            ))),
                ));
                $this->addElement('text', 'facebook_profile_url', array(
                    'placeholder' => 'Facebook',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-facebook-official', 'title' => 'Facebook'), 'placement' => 'PREPEND', 'class' => 'seao_icon_facebook_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'facebook_profile_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addElement('text', 'instagram_profile_url', array(
                    'placeholder' => 'Instagram',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-instagram', 'title' => 'Instagram'), 'placement' => 'PREPEND', 'class' => 'seao_icon_instagram_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'instagram_profile_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addElement('text', 'twitter_profile_url', array(
                    'placeholder' => 'Twitter',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-twitter-square', 'title' => 'Twitter'), 'placement' => 'PREPEND', 'class' => 'seao_icon_twitter_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'twitter_profile_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addElement('text', 'youtube_profile_url', array(
                    'placeholder' => 'Youtube',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-youtube-square', 'title' => 'Youtube'), 'placement' => 'PREPEND', 'class' => 'seao_icon_youtube_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'youtube_profile_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addElement('text', 'vimeo_profile_url', array(
                    'placeholder' => 'Vimeo',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-vimeo-square', 'title' => 'Vimeo'), 'placement' => 'PREPEND', 'class' => 'seao_icon_vimeo_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'vimeo_profile_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addElement('text', 'website_url', array(
                    'placeholder' => 'Website Url',
                    'validators' => array(
                        array('StringLength', true, array(3, 255)),
                    ),
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                    ),
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')),
                        array('Label', array('tag' => 'div', 'tagOptions' => array('class' => 'form-label fa fa-external-link-square', 'title' => 'Website Url'), 'placement' => 'PREPEND', 'class' => 'seao_icon_sharelink_square optional')),
                        array('HtmlTag2', array('tag' => 'div', 'id' => 'website_url-wrapper', 'class' => 'form-wrapper')),
                    ),
                ));
                $this->addDisplayGroup(array('heading', 'facebook_profile_url', 'instagram_profile_url', 'twitter_profile_url', 'youtube_profile_url', 'vimeo_profile_url', 'website_url'), 'socialmedia');
                $this->getDisplayGroup('socialmedia');
            }
        }
        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}
