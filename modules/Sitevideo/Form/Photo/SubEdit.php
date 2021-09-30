<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SubEdit.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Photo_SubEdit extends Engine_Form {

    protected $_isArray = true;

    public function init() {

        $this->clearDecorators()
                ->addDecorator('FormElements');

        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitevideo_edit_media_title')),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => '')),
            ),
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => 'Image Description',
            'rows' => 2,
            'cols' => 120,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitevideo_edit_media_caption')),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => '')),
            ),
        ));

        $this->addElement('Checkbox', 'delete', array(
            'label' => "Delete Photo",
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitevideo_edit_media_options')),
            ),
        ));
        $this->addElement('Hidden', 'file_id', array(
            'order' => 97865,
            'validators' => array(
                'Int',
            )
        ));
    }

}
