<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Playlist_Create extends Engine_Form {

    public function init() {

        // Init form
        $this
                ->setTitle('Create New Playlist')
                ->setAttrib('id', 'form-upload')
                ->setAttrib('name', 'channels_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Playlist Title',
            'maxlength' => '40',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));
        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Playlist Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
        $availableOptions = array(
            'public' => 'Public',
            'private' => 'Private',
        );
        $this->addElement('Select', 'privacy', array(
            'label' => 'Privacy',
            'description' => 'Who may see this playlist?',
            'multiOptions' => $availableOptions,
            'value' => key($availableOptions),
        ));
        $this->privacy->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('File', 'photo', array(
            'label' => 'Main Photo'
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        // Init submit
        $this->addElement('Button', 'playlistsubmit', array(
            'label' => 'Save Playlist',
            'type' => 'submit',
        ));
    }

}
