<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Overview extends Engine_Form {

    public $_error = array();

    public function init() {

        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $siteevent = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitevideo_general', true);
        }

        $this->setTitle("Edit Channel Overview")
                ->setDescription("Edit the overview for your channel using the editor below, and then click 'Save Overview' to save changes.")
                ->setAttrib('name', 'channel_overview');
        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url);
        $editorOptions['height'] = '600px';
        $editorOptions['width'] = '880px';
        $this->addElement('TinyMce', 'overview', array(
            'label' => '',
            'allowEmpty' => false,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Overview',
            'type' => 'submit',
        ));
    }

}
