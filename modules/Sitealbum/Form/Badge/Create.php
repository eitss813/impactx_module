<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Badge_Create extends Engine_Form {

  public function init() {
    $this->setTitle('Your Photos Badge');
    $this->setAttrib('name', 'badge_create');
    $this->setDescription('Create a photos badge to show off your photos on your external blog or website. You can customize the way your photos are displayed. Copy the generated HTML and paste it into the source code for your web page.');

    $type = array('recent' => 'Recent',
        'liked' => 'Most Liked',
        'commented' => 'Most Commented',
        'viewed' => 'Most Viewed',
        'random' => 'Random',
        'album' => 'Album Specific');
    $this->addElement('Select', 'type', array(
        'label' => 'Photos to Show',
        'attribs' => array('style' => 'max-width:200px;'),
        'MultiOptions' => $type,
        'onchange' => "showDropDown(this.value)"
    ));
    // Init album
    $this->addElement('Select', 'album', array(
        'label' => 'Select Album',
        'attribs' => array('style' => 'max-width:200px;'),
        'onchange' => "previewBadge(1)"
    ));

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
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));


    $this->addElement('Text', 'no_of_image', array(
        'label' => 'Maximum Number of Photos',
        'allowEmpty' => false,
        'required' => true,
        'maxlength' => '2',
     //   'description' => 'show maximum images',
        'value' => 10,
        'attribs' => array('style' => 'width:80px; max-width:80px;'),
        'onblur' => "previewBadge(1)",
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
        'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));

    $this->addElement('Text', 'background_color', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '/badge/color/_rainbowBackground.tpl',
                    'class' => 'form element'
            ))),
        'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));

    $this->addElement('Text', 'border_color', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '/badge/color/_rainbowBorder.tpl',
                    'class' => 'form element'
            ))),
        'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));

    $this->addElement('Text', 'text_color', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '/badge/color/_rainbowText.tpl',
                    'class' => 'form element'
            ))),
        'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));
    $this->addElement('Text', 'link_color', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '/badge/color/_rainbowLink.tpl',
                    'class' => 'form element'
            ))),
        'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
    ));

    $this->addElement('hidden', 'owner', array(
        'order' => 568,
    ));
    $this->addElement('Button', 'get_source', array(
        'label' => 'Create Badge',
        'onClick' => "previewBadge(0)",
    ));
  }

}