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
class Sitecrowdfunding_Form_Project_Create_StepSeven extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setAttrib('id', 'sitecrowdfunding_project_new_step_seven_custom')
            ->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'no_of_jobs', array(
            'label' => "How many jobs will this project create if any?",
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->no_of_jobs->getDecorator('Description')->setOption('placement', 'append');

        // TinyMcs Settings

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitecrowdfunding_general', true);
        }

        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url);
        $editorOptions['height'] = '600px';
        $editorOptions['width'] = '100%';
        $editorOptions['toolbar1'] = array(
            'undo', 'redo', 'removeformat', 'pastetext', '|', 'code', 'image', 'jbimages', 'link', 'fullscreen',
            'preview'
        );

        $this->addElement('TinyMce', 'help_desc', array(
            'label' => "Paying it Forward",
            'description' => 'How will you help others? (paying it forward)',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

    }

}
