<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Overview extends Engine_Form {

    public $_error = array();

    public function init() {

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitecrowdfunding_general', true);
        }
        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url);
        $editorOptions['height'] = '600px';
        $editorOptions['width'] = '100%';
        $editorOptions['fontsize_formats'] = '18pt';

        $editorOptions['toolbar1'] = array(
            'undo', 'redo', 'removeformat', 'pastetext', '|', 'code', 'image', 'jbimages', 'link', 'fullscreen',
            'preview'
        );
        $location = Zend_Controller_Front::getInstance()->getRequest()->getParam('location', null);

        // Text
        $this->addElement('Text', 'title', array(
            'label' => "Business Name or Project Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('TinyMce', 'desire_desc', array(
            'label' => "Provide a short 2 -3 sentence description of the project to interest people",
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
            'required' => true,
        ));


        $this->setTitle("Edit About The Project")
                //->setDescription("Explain what you plan to do and why it is important.")
                ->setAttrib('name', 'project_overview');

        $this->addElement('TinyMce', 'overview', array(
            'label' => 'About The Project',
            'description' => 'Explains what the project plans to do and what impacts it can have.',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

        //@todo : client feedback merge backstory with about
        $this->addElement('TinyMce', 'description', array(
            'label' => "Backstory",
            'description'=> 'Explains the history behind this project. Explain who you are and how you got involved.  What have you achieved so far?  Who has helped?  What have been the biggest challenges?',
            'required' => true,
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));


        $this->addElement('TinyMce', 'help_desc', array(
            'label' => "Paying it Forward",
            'description' => 'How will you help others? (paying it forward)',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

        // Project Location
        $this->addElement('Text', 'location', array(
                'label' => 'Project Location',
                'description' => 'Eg: Fairview Park, Berkeley, CA',
                'placeholder' => $view->translate('Enter a location'),
                'autocomplete' => 'off',
                'required' => true,
                'allowEmpty' => false,
                'value' => $location,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                ))
        );
        $this->location->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Hidden', 'locationParams', array('order' => 800000));

        include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';





        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
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
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "sitecrowdfunding_general", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array(
            'save',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
