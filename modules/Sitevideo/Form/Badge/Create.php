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
class Sitevideo_Form_Badge_Create extends Engine_Form {

    public function init() {
        $this->setTitle('Your Videos Badge');
        $this->setAttrib('name', 'badge_create');
        $this->setDescription('Create a videos badge to show off your videos on your external blog or website. You can customize the way your videos are displayed. Copy the generated HTML and paste it into the source code for your web page.');

        $this->addElement('Text', 'width', array(
            'label' => 'Badge Width (px)',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '3',
            'value' => 180,
            'attribs' => array('style' => 'width:80px; max-width:80px;'),
            'onblur' => "previewBadge(1)",
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));


        $this->addElement('Text', 'no_of_image', array(
            'label' => 'Maximum Number of Videos',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '2',
            'value' => 10,
            'attribs' => array('style' => 'width:80px; max-width:80px;'),
            'onblur' => "previewBadge(1)",
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        $this->addElement('Text', 'background_color', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '/badge/color/_rainbowBackground.tpl',
                        'class' => 'form element'
                    ))),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        $this->addElement('Text', 'border_color', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '/badge/color/_rainbowBorder.tpl',
                        'class' => 'form element'
                    ))),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        $this->addElement('Text', 'text_color', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '/badge/color/_rainbowText.tpl',
                        'class' => 'form element'
                    ))),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));
        $this->addElement('Text', 'link_color', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '/badge/color/_rainbowLink.tpl',
                        'class' => 'form element'
                    ))),
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        $this->addElement('hidden', 'owner', array('order' => 10001,
        ));
        $this->addElement('Button', 'get_source', array(
            'label' => 'Create Badge',
            'onClick' => "previewBadge(0)",
        ));
    }

}
