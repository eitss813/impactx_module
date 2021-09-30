<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Create_StepSixVideo extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Upload Video")))
            ->getDecorator('Description')->setOption('escape', false);

        // Init url
        $this->addElement('Text', 'url', array(
            'label' => 'Youtube Video Link (URL)',
            'onkeyup' => 'fetchYoutubeData(this.value)',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
            ),
            'required' => true,
            'description' => 'Paste the youtube url of the video here.',
            'maxlength' => '5000'
        ));
        $this->url->getDecorator("Description")->setOption("placement", "append");

        $this->addElement('Text', 'video_title', array(
            'label' => 'Video Title',
            'maxlength' => '100',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));

        // Init descriptions
        $this->addElement('Textarea', 'video_description', array(
            'label' => 'Video Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $this->addElement('Hidden', 'video_type', array(
            'value' => 'youtube',
            'order' => 80001
        ));

        $this->addElement('Hidden', 'video_duration', array(
            'order' => 80002
        ));

        $this->addElement('Hidden', 'video_code', array(
            'order' => 80003
        ));

        $this->addElement('Hidden', 'video_valid', array(
            'order' => 80004
        ));


        $this->addElement('Button', 'execute', array(
            'label' => 'Upload Video',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => 'javascript:void(0);',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));


    }

}
