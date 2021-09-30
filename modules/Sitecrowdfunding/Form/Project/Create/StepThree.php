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
class Sitecrowdfunding_Form_Project_Create_StepThree extends Engine_Form
{

    public function init()
    {
        $this->loadDefaultDecorators();
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->setTitle('Create A Project')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_create');

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $location = Zend_Controller_Front::getInstance()->getRequest()->getParam('location', null);

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // Text
        $this->addElement('Text', 'title', array(
            'label' => "Business Name or Project Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));




        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitecrowdfunding_general', true);
        }

        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
        $editorOptions['height'] = '250px';
        $editorOptions['width'] = '80%';
        $editorOptions['toolbar1'] = array(
            'undo', 'redo', 'removeformat', 'pastetext', '|', 'code', 'jbimages', 'link', 'fullscreen',
            'preview'
        );

        $this->addElement('TinyMce', 'desire_desc', array(
            'label' => "Provide a short 2 -3 sentence description of the project to interest people",
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
            'required' => true,
        ));

        $this->addElement('TinyMce', 'overview', array(
            'label' => 'About The Project',
            'description' => 'Explain what the project plans to do and what impacts it can have.',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
            'required' => true,

        ));



        // Description
        $this->addElement('textarea', 'description', array(
            'label' => "Backstory",
            'description' => 'Explain the history behind this project.  Explain who you are and how you got involved.  What have you achieved so far?  Who has helped?  What have been the biggest challenges?',
            'required' => true,
            'allowEmpty' => false,
            'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'height:120px;'),
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('TinyMce', 'help_desc', array(
            'label' => "Paying it Forward",
            'description' => 'How will you help others? (paying it forward)',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

            // Project Start Date
            $this->addElement('CalendarDateTimeCustom', 'starttime', array(
                'label' => 'Project Start Date',
                'allowEmpty' => true,
                'required' => false
            ));

            // Project End Date
            $this->addElement('CalendarDateTimeCustom', 'endtime', array(
                'label' => 'Project End Date',
                'allowEmpty' => true,
                'required' => false
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

        $this->addElement('Button', 'execute', array(
            'label' => 'Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-one',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->addElement('Button', 'previous', array(
            'label' => 'Previous',
            'onclick' => "window.location.href='".$backURL."'",
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

    }

}
