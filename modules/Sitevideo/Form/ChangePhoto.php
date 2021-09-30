<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChangePhoto.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_ChangePhoto extends Engine_Form {

    public function init() {

        $channel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('channel_id', null);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        $this->setTitle("Edit Profile Picture")
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'EditPhoto');

        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formEditImage.tpl',
                        'class' => 'form element',
                        'testing' => 'testing'
                    )))
        ));
        Engine_Form::addDefaultDecorators($this->current);

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();'
        ));

        $this->addElement('Hidden', 'coordinates', array(
            'order' => 10001,
            'filters' => array(
                'HtmlEntities',
            )
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'remove-photo', 'channel_id' => $channel_id), "sitevideo_dashboard", true);
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if ($channel->file_id != 0) {

            $this->addElement('Button', 'remove', array(
                'label' => 'Remove Photo',
                'onclick' => "removePhotoChannel('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $url = $view->url(array('channel_url' => $channel->channel_url, 'slug' => $channel->getSlug()), "sitevideo_entry_view", true);

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' ' . Zend_Registry::get('Zend_Translate')->_('or') . ' ',
                'link' => true,
                'onclick' => "removePhotoChannel('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
        }
    }

}
