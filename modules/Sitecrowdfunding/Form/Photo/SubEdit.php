<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SubEdit.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Photo_SubEdit extends Engine_Form {

    protected $_isArray = true;

    public function init() {

        $this->clearDecorators()
                ->addDecorator('FormElements');

        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitecrowdfunding_edit_media_title')),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => '')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'sitecrowdfunding_edit_media_title_wrapper')),
            ),
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => 'Image Description',
            'rows' => 2,
            'cols' => 120,
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitecrowdfunding_edit_media_caption')),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => '')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'sitecrowdfunding_edit_media_title_wrapper')),
            ),
        ));

        $this->addElement('Checkbox', 'delete', array(
            'label' => "Delete Photo",
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'sitecrowdfunding_edit_media_options')),
            ),
        ));
        $this->addElement('Hidden', 'file_id', array(
            'validators' => array(
                'Int',
            )
        ));
    }

}
