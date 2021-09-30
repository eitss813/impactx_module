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
class Sitevideo_Form_Post_Create extends Engine_Form {

    public function init() {

        $content_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id', null);
        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id');
        $topic_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('topic_id');
        $this
                ->setTitle('Reply')
                ->setAction(
                        Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble(array('action' => 'post', 'controller' => 'topic', 'content_id' => $content_id, 'channel_id' => $channel_id, 'topic_id' => $topic_id), "sitevideo_topic_extended", true)
        );


        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.tinymceditor', 1)) {
            $this->addElement('Textarea', 'body', array(
                'label' => 'Body',
                'required' => true,
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        } else {
            $this->addElement('TinyMce', 'body', array(
                'label' => 'Body',
                'allowEmpty' => false,
                'required' => true,
                'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
                'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
                'filters' => array(new Engine_Filter_Censor()),
            ));
        }

        $this->addElement('Checkbox', 'watch', array(
            'label' => 'Send me notifications when other members reply to this topic.',
            'value' => '1',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Post Reply',
            'ignore' => true,
            'type' => 'submit',
        ));

        $this->addElement('Hidden', 'topic_id', array(
            'order' => 1000110,
            'filters' => array(
                'Int'
            )
        ));

        $this->addElement('Hidden', 'ref',array('order' => 1000111));
    }

}
