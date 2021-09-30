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
class Sitecrowdfunding_Form_Additional extends Engine_Form {

    public $_error = array();

    public function init() {

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();


        $this->setTitle("Additional Section")
            ->setAttrib('name', 'title');


        $this->addElement('Text', 'title', array(
            'label' => "Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);


        //GET TINYMCE SETTINGS
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


        //@todo : client feedback merge backstory with about
        $this->addElement('TinyMce', 'description', array(
            'label' => "Details",
            'required' => true,
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));



        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));



    }

}
